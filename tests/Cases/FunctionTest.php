<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace HyperfTest\Cases;

use Hyperf\HttpMessage\Uri\Uri;

use function Han\Utils\csv_open;
use function Han\Utils\http_parse_query;
use function Han\Utils\is_continuous;
use function Han\Utils\optional;
use function Han\Utils\unset_uri_param;

/**
 * @internal
 * @coversNothing
 */
class FunctionTest extends AbstractTestCase
{
    public function testOptional()
    {
        $this->assertNull(optional(null)->id);
    }

    public function testCsvOpen()
    {
        $fp = csv_open(__DIR__ . '/../../runtime/' . uniqid() . '.csv');

        fputcsv($fp, ['ID', 'Name']);
        fputcsv($fp, ['1', 'hyperf']);

        fclose($fp);

        $this->assertTrue(true);
    }

    public function testHttpParseQuery()
    {
        $res = http_parse_query('id=1&name=limx');
        $this->assertSame(['id' => '1', 'name' => 'limx'], $res);

        $res = http_parse_query('id=1&name=' . urlencode(json_encode(['id' => 1])));
        $this->assertSame(['id' => '1', 'name' => json_encode(['id' => 1])], $res);
    }

    public function testUnsetUriParam()
    {
        $uri = new Uri('http://xxx.com?id=1&json=' . urlencode(json_encode(['id' => 1])));

        $this->assertSame('http://xxx.com?json=' . urlencode(json_encode(['id' => 1])), (string) unset_uri_param($uri, 'id'));

        $uri = new Uri('http://xxx.com?id=1&json=' . urlencode(json_encode(['id' => 1])));

        $this->assertSame('http://xxx.com?id=1', (string) unset_uri_param($uri, 'json'));
    }

    public function testIsContinuous()
    {
        $this->assertTrue(is_continuous([[1, 5], [5, 6], [6, 10]])->isContinuous());
        $this->assertFalse(is_continuous([[1, 5], [5, 6], [6.1, 10]])->isContinuous());
        $this->assertTrue(is_continuous([[1, 5], [5, 6], [5.9, 10]])->isContinuous());
        $this->assertTrue(is_continuous([[1, 5], [5, 10], [6, 10]])->isContinuous());

        $ret = is_continuous([[1, 5], [5, 10], [6, 10]]);
        $this->assertSame(1, $ret->min);
        $this->assertSame(10, $ret->max);
    }
}
