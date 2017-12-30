<?php

namespace AppBundle\Service;

use Doctrine\Common\Cache\FilesystemCache;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

class MarkdownTransformer
{
    private $markdownParser;
    private $filesystemCache;

    public function __construct(MarkdownParserInterface $markdownParser, FilesystemCache $filesystemCache)
    {
        $this->markdownParser = $markdownParser;
        $this->filesystemCache = $filesystemCache;
    }

    public function parse($str)
    {
        $cache = $this->filesystemCache;
        $key = md5($str);
        if ($cache->contains($key)) {
            return $cache->fetch($key);
        }

        sleep(2);
        $str = $this->markdownParser->transformMarkdown($str);
        $cache->save($key, $str);
        return $str;
    }
}
