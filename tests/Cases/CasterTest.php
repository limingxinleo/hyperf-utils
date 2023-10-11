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

use Carbon\Carbon;
use Han\Utils\Caster\IntegerItems;
use Han\Utils\Caster\IntegerItemsCaster;
use Han\Utils\Caster\SafeDateTimeCaster;
use Han\Utils\Utils\Date;
use Hyperf\Database\Model\Model;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
class CasterTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        ApplicationContext::setContainer(
            $container = \Mockery::mock(ContainerInterface::class)
        );
        $container->shouldReceive('get')->with(Date::class)->andReturn(new Date());
    }

    protected function tearDown(): void
    {
        \Mockery::close();
    }

    public function testIntegerCaster()
    {
        $foo = new Foo(['user_ids' => '1,2,3']);
        $this->assertInstanceOf(IntegerItems::class, $foo->user_ids);
        $this->assertSame([1, 2, 3], $foo->user_ids->toArray());
    }

    public function testSafeDateTimeCaster()
    {
        $foo = new Foo(['online_at' => '2021-12-11 13:09:00']);
        $this->assertInstanceOf(Carbon::class, $foo->online_at);
        $this->assertSame(1639199340, $foo->online_at->getTimestamp());

        $foo->online_at = $now = Carbon::now();
        $foo->syncAttributes();
        $this->assertSame(['online_at' => $now->toDateTimeString()], $foo->getAttributes());

        $foo = new Foo(['online_at' => '']);
        $this->assertNull($foo->online_at);

        $foo = new Foo(['online_at' => null]);
        $this->assertNull($foo->online_at);

        $foo = new Foo([]);
        $this->assertNull($foo->online_at);
    }
}

/**
 * @property IntegerItems $user_ids
 * @property Carbon $online_at
 */
class Foo extends Model
{
    protected array $fillable = ['user_ids', 'online_at'];

    protected array $casts = ['user_ids' => IntegerItemsCaster::class, 'online_at' => SafeDateTimeCaster::class];
}
