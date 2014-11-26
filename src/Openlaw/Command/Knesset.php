<?php

namespace Openlaw\Command;

use Openlaw\Data\Booklet;
use Openlaw\Data\Part;
use Openlaw\Silex\Provider\Command;

class Knesset extends Command
{
    protected $config = [];

    public function __construct()
    {
        $this->config = [
          'final_laws' => [
            'source' => 'http://www.knesset.gov.il/laws/heb/template.asp?Type=1',
            'single_page_base' => 'http://www.knesset.gov.il/laws/heb/template.asp?Type=1&FmNum=',
            'base_url' => 'http://www.knesset.gov.il',
          ],
        ];
    }

    public function fetchBooklet()
    {
        $html = file_get_contents($this->config['final_laws']['source']);

        $result = [
          'new' => [],
          'updated' => [],
        ];

        $knesset_booklets = $this->parseBooklet($html);
        foreach ($knesset_booklets as $knesset_booklet) {
            $booklet = Booklet::factory((int) $knesset_booklet['booklet']);
            $knesset_booklet_part = (int) explode('_', basename($knesset_booklet['link'], '.pdf'))[1];
            $knesset_single_page = $this->config['final_laws']['single_page_base'] . $knesset_booklet['booklet'];
            if (empty($booklet->booklet)) {
                $this->populateBooklet($booklet, $knesset_booklet);
                $booklet_part = Part::factory();
                $this->populatePart($booklet_part, $knesset_booklet);
                $booklet->parts = [
                  $booklet_part,
                ];
                $result['new'][$booklet->booklet] = $booklet;
            } else {
                $booklet_updated = false;
                if (empty($booklet->knesset)) {
                    $booklet->knesset = (int) $knesset_booklet['knesset'];
                    $booklet_updated = true;
                }
                if (empty($booklet->links)) {
                    $booklet->links = [];
                }
                if (!isset($booklet->links['knesset_single_page'])) {
                    $booklet->links = array_merge(
                      $booklet->links,
                      [
                        'knesset_single_page' => $knesset_single_page,
                      ]
                    );
                    $booklet_updated = true;
                }
                if ($booklet_updated) {
                    $booklet->updated = time();
                    $booklet->save();
                    $result['updated'][$booklet->booklet] = $booklet;
                }

                $booklet_part = Part::factory((int) $knesset_booklet['booklet'], (int) $knesset_booklet_part);
                if (empty($booklet_part->_id)) {
                    $this->populatePart($booklet_part, $knesset_booklet);
                    $result['new'][$booklet->booklet]->parts = array_merge(
                      $result['new'][$booklet->booklet]->parts,
                      [
                        $booklet_part,
                      ]
                    );
                }
            }
        }

        return $result;
    }

    protected function populateBooklet(Booklet &$booklet, $knesset_booklet = [])
    {
        $booklet->unpack(
          [
            'booklet' => (int) $knesset_booklet['booklet'],
            'knesset' => (int) $knesset_booklet['knesset'],
            'links' => [
              'knesset_single_page' => $this->config['final_laws']['single_page_base'] . $knesset_booklet['booklet'],
            ],
            'created' => time(),
          ]
        );
        $booklet->save();
    }

    protected function populatePart(Part &$part, $knesset_booklet = [])
    {
        $part->unpack(
          [
            'booklet' => (int) $knesset_booklet['booklet'],
            'links' => [
              'knesset_pdf' => $knesset_booklet['link'],
            ],
            'origin' => [
              'knesset_title' => $knesset_booklet['title'],
              'knesset_part' => (int) (explode('_', basename($knesset_booklet['link'], '.pdf'))[1]),
            ],
            'created' => time(),
          ]
        );
        $part->save();
    }

    protected function parseBooklet($html = '')
    {
        $doc = new \DOMDocument('1.0', 'windows-1255');
        @$doc->loadHTML($html);

        $xpath = new \DOMXPath($doc);

        $law_list = $xpath->query('//center/table/tr');

        // Preset variables
        $column_map = [
          'knesset',
          'booklet',
          'title',
          'link',
        ];
        $laws = [];
        $previous_law = [];

        foreach ($law_list as $table_row) {
            // Skip if the row is irrelevant
            /** @var \DOMElement $table_row */
            if ((string) $table_row->firstChild->getAttribute('class') != 'LawText1') {
                continue;
            }

            // Set previous law for missing info filling
            if (isset($law)) {
                $previous_law = $law;
            }

            // Preset variables
            $law = [];
            $map_index = 0;

            // Run through columns
            $td_list = $xpath->query('./td', $table_row);
            foreach ($td_list as $table_data) {
                if ($map_index == 3) {
                    /** @var \DOMElement $link */
                    $link = $xpath->query('./a', $table_data)->item(0);
                    $full_path = $link->getAttribute('href');
                    if (!empty($full_path)) {
                        $full_path = $this->config['final_laws']['base_url'] . $full_path;
                    }
                    $law[$column_map[$map_index]] = $full_path;
                } elseif ($map_index == 2) {
                    $law[$column_map[$map_index]] = $this->sanitizeString($table_data->nodeValue);
                } else {
                    $law[$column_map[$map_index]] = trim($table_data->nodeValue);
                    if ($map_index == 1 && empty($law[$column_map[$map_index]])) {
                        $law[$column_map[$map_index]] = $previous_law[$column_map[$map_index]];
                    }
                }
                $map_index++;
            }
            $laws[] = $law;
        }

        return $laws;
    }

    protected function sanitizeString($string = '')
    {
        // Replace strings with strings
        $string = str_replace(array(';', '–', '―'), array(',', '-', '-'), $string);
        // Replace un-wanted single quotes with simple single quote
        $string = str_replace(array('‘', '`'), '\'', $string);
        // Replace un-wanted double-quotes with simple double-quote
        $string = str_replace(array('״', '”', '“'), '"', $string);
        // Remove strings
        $string = str_replace(array('=', '\\', '?', json_decode('"\u200f"')), '', $string);
        // Replace unicode spaces with simple space
        $string = str_replace(array(json_decode('"\u2002"'), json_decode('"\u2003"'), '  '), ' ', $string);
        // Replace multiple spaces with one space
        $string = preg_replace('/[\s\t]+/', ' ', $string);
        // Remove white spaces from beginning and end
        $string = trim($string);

        return $string;
    }
}
