<?php

namespace FormHandler;
use \FormHandler\Utils;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-12-28 at 11:07:30.
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public function dataProviderTestHtml()
    {
        return array(
            1 => array('https://phpunit.formhandlet.net/', 'https:&#x2F;&#x2F;phpunit.formhandlet.net&#x2F;'),
            2 => array('https://phpunit.formhandlet.net/<script>', 'https:&#x2F;&#x2F;phpunit.formhandlet.net&#x2F;&lt;script&gt;'),
            3 => array('https://phpunit.formhandlet.net/<script>alert(\'1\')</script>', 'https:&#x2F;&#x2F;phpunit.formhandlet.net&#x2F;&lt;script&gt;alert(&#x27;1&#x27;)&lt;&#x2F;script&gt;'),
            4 => array('https://phpunit.formhandlet.net/<script>alert("1")</script>', 'https:&#x2F;&#x2F;phpunit.formhandlet.net&#x2F;&lt;script&gt;alert(&quot;1&quot;)&lt;&#x2F;script&gt;'),
            5 => array('https://phpunit.formhandlet.net/test.php?">=', 'https:&#x2F;&#x2F;phpunit.formhandlet.net&#x2F;test.php?&quot;&gt;='),
            6 => array('phpunit.formhandlet.net/test.php?">', 'phpunit.formhandlet.net&#x2F;test.php?&quot;&gt;'),
            7 => array('test.php?">', 'test.php?&quot;&gt;'),
        );
    }

    /**
     * @dataProvider dataProviderTestHtml
     */
    public function testHtml($input, $expected)
    {
        $this->assertEquals($expected, Utils::html($input));
    }

    public function dataProviderTestUrl()
    {
        return array(
            array('phpunit.formhandlet.net/test/"><script>', 'http://phpunit.formhandlet.net/test/%22%3E%3Cscript%3E'),
            array('//test.phpunit.formhandlet.net:443/form/handler/index.php?firstparam=1&secondparam=2#str', '//test.phpunit.formhandlet.net:443/form/handler/index.php?firstparam=1&secondparam=2#str'),
            array('https://phpunit.formhandlet.net:443/form/handler/index.php?firstparam=1&secondparam=2#str', 'https://phpunit.formhandlet.net:443/form/handler/index.php?firstparam=1&secondparam=2#str'),
            array('phpunit.formhandlet.net/form/handler/index.php', 'http://phpunit.formhandlet.net/form/handler/index.php'),
            array('//phpunit.formhandlet.net?test=test1&secondparam=">', '//phpunit.formhandlet.net?test=test1&secondparam=%22%3E'),
            array('./test.php', './test.php'),
            array('https://phpunit.formhandlet.net/<script>', 'https://phpunit.formhandlet.net/%3Cscript%3E'),
            array('/form/handler/index.php', '/form/handler/index.php'),
            array('https://phpunit.formhandlet.net/', 'https://phpunit.formhandlet.net/'),
            array('https://phpunit.formhandlet.net/<script>alert(\'1\')</script>', 'https://phpunit.formhandlet.net/%3Cscript%3Ealert%28%271%27%29%3C/script%3E'),
            array('https://phpunit.formhandlet.net/<script>alert("1")</script>', 'https://phpunit.formhandlet.net/%3Cscript%3Ealert%28%221%22%29%3C/script%3E'),
            array('https://phpunit.formhandlet.net/"><script>', 'https://phpunit.formhandlet.net/%22%3E%3Cscript%3E'),
            array('https://phpunit.formhandlet.net/test.php?">', 'https://phpunit.formhandlet.net/test.php?%22%3E='),
            array('phpunit.formhandlet.net/test.php?">', 'http://phpunit.formhandlet.net/test.php?%22%3E='),
            array('./test.php?">', './test.php?%22%3E='),
            array('./test.php/">', './test.php/%22%3E'),
            array('http://phpunit.formhandlet.net/test/%2F%22%3E%3Cscript%3E', 'http://phpunit.formhandlet.net/test/%2F%22%3E%3Cscript%3E'),
            array('http://phpunit.formhandlet.net/test/%252F%2522%253E%253Cscript%253E', 'http://phpunit.formhandlet.net/test/%252F%2522%253E%253Cscript%253E'),
            array('https://phpunit.formhandlet.net/<script>al/ert("1")</script>', 'https://phpunit.formhandlet.net/%3Cscript%3Eal/ert%28%221%22%29%3C/script%3E'),
        );
    }

    /**
     * @dataProvider dataProviderTestUrl
     */
    public function testUrl($input, $expected)
    {
        $this->assertEquals($expected, Utils::url($input));
    }

    /**
     * @covers Utils::buildRequestUrl
     */
    public function testbuildRequestUrl()
    {
        $list = array(
            'https://phpunit.formhandlet.net/<script>' => 'https://phpunit.formhandlet.net/%3Cscript%3E',
            'https://phpunit.formhandlet.net/index.php?test=1&2=<script>' => 'https://phpunit.formhandlet.net/index.php?test=1&2=%3Cscript%3E',
            'https://phpunit.formhandlet.net/<script>alert(\'1\')</script>' => 'https://phpunit.formhandlet.net/%3Cscript%3Ealert%28%271%27%29%3C/script%3E',
            'https://phpunit.formhandlet.net/<script>alert("1")</script>' => 'https://phpunit.formhandlet.net/%3Cscript%3Ealert%28%221%22%29%3C/script%3E',
            'https://phpunit.formhandlet.net/test.php?">' => 'https://phpunit.formhandlet.net/test.php?%22%3E=',
            'https://phpunit.formhandlet.net/test.php?test=1&a=">\'' => 'https://phpunit.formhandlet.net/test.php?test=1&a=%22%3E%27',
        );

        foreach($list as $input => $check)
        {
            $url = parse_url($input);

            $protocol = array_key_exists('scheme', $url)
                ? $url['scheme']
                : 'http';

            $host = array_key_exists('host', $url)
                ? $url['host']
                : 'localhost';

            if(array_key_exists('path', $url))
            {
                $host .= $url['path'];
            }

            $query = array_key_exists('query', $url)
                ? '?'.$url['query']
                : '';

            $this->assertEquals($check, Utils::buildRequestUrl($protocol, $host, $query));
        }
    }
}
