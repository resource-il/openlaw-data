<?php

namespace Openlaw\Command;

use Openlaw\Data\Booklet;
use Openlaw\Silex\Provider\Command;

class Justice extends Command
{
    use Utils;

    public function __construct()
    {
        $this->config = [
          'final_laws' => [
            'source' => 'http://www.justice.gov.il/rssnews/generalrss.aspx?rssid=18',
            'base_url' => 'http://old.justice.gov.il',
          ],
        ];
    }

    public function fetchBooklet()
    {
        $rss = file_get_contents($this->config['final_laws']['source']);

        $result = [
          'new' => [],
          'updated' => [],
        ];

        $justice_booklets = $this->parseBooklet($rss);

        foreach ($justice_booklets as $justice_booklet) {
            $booklet = Booklet::factory($justice_booklet['booklet']);
            $booklet_html = file_get_contents($justice_booklet['single_page']);
            $this->parseBookletInfo($booklet_html, $justice_booklet);

            if (!$booklet->_id) {
                $this->populateBooklet($booklet, $justice_booklet);
                $result['new'][$booklet->booklet] = $booklet;
                break;
            } else {
                $updated = false;
                if (empty($booklet->links)) {
                    $booklet->links = [];
                }
                if (empty($booklet->links['justice_pdf']) || empty($booklet->links['justice_single_page'])) {
                    $booklet->links = array_merge($booklet->links, [
                          'justice_pdf' => $justice_booklet['pdf'],
                          'justice_single_page' => $justice_booklet['single_page'],
                      ]);
                    $updated = true;
                }

                // Published date
                if (empty($booklet->dates)) {
                    $booklet->dates = [];
                }
                if (empty($booklet->dates['published'])) {
                    $booklet->dates = array_merge($booklet->dates, [
                          'published' => $justice_booklet['pub_date'],
                      ]);
                    $updated = true;
                }

                // Origin
                if (empty($booklet->origin)) {
                    $booklet->origin = [];
                }
                if (empty($booklet->origin['justice_description'])) {
                    $booklet->origin = array_merge($booklet->origin, [
                          'justice_description' => $justice_booklet['description'],
                      ]);
                    $updated = true;
                }

                if ($updated) {
                    $booklet->updated = time();
                    $booklet->save();
                    $result['updated'][$booklet->booklet] = $booklet;
                }
            }
        }

        return $result;
    }

    protected function populateBooklet(Booklet &$booklet, $justice_booklet = [])
    {
        $booklet->unpack(
          [
            'booklet' => (int) $justice_booklet['booklet'],
            'links' => [
              'justice_pdf' => $justice_booklet['pdf'],
              'justice_single_page' => $justice_booklet['single_page'],
            ],
            'dates' => [
              'published' => $justice_booklet['pub_date'],
            ],
            'origin' => [
              'justice_description' => $justice_booklet['description'],
            ],
            'created' => time(),
          ]
        )->save();
    }

    protected function parseBooklet($rss = '')
    {
        $xml = @simplexml_load_string($rss);

        $items = [];
        foreach ($xml->channel->item as $item) {
            if ((bool) $item->title != (int) $item->title) {
                continue;
            }

            $rssPubDate = new \DateTime((string) $item->pubDate);
            $rssPubDate->setTimezone(new \DateTimeZone('Asia/Jerusalem'));

            $items[] = [
              'booklet' => (int) $item->title,
              'single_page' => (string) $item->link,
              'rss_pub_date' => $rssPubDate->format('Y-m-d H:i:s'),
            ];
        }

        return $items;
    }

    protected function parseBookletInfo($html = '', array &$justice_booklet = [])
    {
//        $html = mb_convert_encoding($html, 'UTF-8', 'HTML-ENTITIES');

        // Get the PDF file and the publication date
        $doc = new \DOMDocument('1.0', 'utf-8');
        @$doc->loadHTML($html);

        $xpath = new \DOMXPath($doc);

        $table = $xpath->query('//table[@id="Table1"]')->item(0);
        $second_row = $xpath->query('//tr[2]/td[not(@bgcolor)]', $table);

        $pdf_link = $xpath->query('//a', $second_row->item(0))->item(0)->attributes->getNamedItem('href')->nodeValue;
        $pub_date = preg_replace('#^(\d+)/(\d+)/(\d+)$#', '\3-\2-\1', trim($second_row->item(1)->nodeValue));
        $description = $this->sanitizeString($xpath->query('//tr[3]/td[2]', $table)->item(0)->nodeValue);

        $justice_booklet['pdf'] = $this->config['final_laws']['base_url'] . $pdf_link;
        $justice_booklet['pub_date'] = $pub_date;
        $justice_booklet['description'] = $description;
    }
}
