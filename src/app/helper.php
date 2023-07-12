<?php

declare (strict_types=1);

use App\Constants\Constant;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Hyperf\Amqp\Message\ProducerMessageInterface;
use Hyperf\Amqp\Producer;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\JobInterface;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\DbConnection\Db;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\RedisFactory;
use Hyperf\Redis\RedisProxy;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Utils\ApplicationContext;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


if (!function_exists('di')) {
    /**
     * 获取di容器.
     */
    function di(): ContainerInterface
    {
        return ApplicationContext::getContainer();
    }
}

if (!function_exists('formatThrowable')) {
    /**
     * Format a throwable to string.
     * @param Throwable $throwable
     * @return string
     */
    function formatThrowable(Throwable $throwable): string
    {
        return di()->get(FormatterInterface::class)->format($throwable);
    }
}

if (!function_exists('queuePush')) {
    /**
     * Push a job to async queue.
     */
    function queuePush(JobInterface $job, int $delay = 0, string $key = 'default'): bool
    {
        $driver = di()->get(DriverFactory::class)->get($key);
        return $driver->push($job, $delay);
    }
}

if (!function_exists('amqpProduce')) {
    /**
     * Produce a amqp message.
     */
    function amqpProduce(ProducerMessageInterface $message): bool
    {
        return di()->get(Producer::class)->produce($message, true);
    }
}
/**
 * 组装分页数据格式
 */
if (!function_exists('getPageData')) {
    function getPageData(LengthAwarePaginatorInterface $paginateData)
    {
        return [
            'total' => $paginateData->total(),
            'page' => $paginateData->currentPage(),
            'page_size' => $paginateData->perPage(),
            'list' => $paginateData->items(),
        ];
    }
}
/**
 * 获取当前时间戳(毫秒)
 *
 * @return void
 */
if (!function_exists('getMillisecond')) {
    function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}

if (!function_exists('redis')) {
    /**
     * 获取redis连接池实例.
     */
    function redis(string $name = 'default'): RedisProxy
    {
        return di()->get(RedisFactory::class)->get($name);
    }
}

if (!function_exists('getCache')) {
    /**
     * 获取redis连接池实例.
     */
    function getCache(string $key): string
    {
        $redis = di()->get(RedisFactory::class)->get('cache');
        return $redis->get($key);
    }
}

if (!function_exists('setCache')) {
    /**
     * 获取redis连接池实例.
     * @param mixed $val
     * @param mixed $ttl
     */
    function setCache(string $key, int $val, $ttl = 3600 * 24 * 360): bool
    {
        $redis = di()->get(RedisFactory::class)->get('cache');

        if (!is_string($val)) {
            $val = serialize($val);
        }
        return $redis->setex($key, $val, $ttl);
    }
}

if (!function_exists('logger')) {
    /**
     * 获取指定日志实例.
     */
    function logger(string $name = 'hyperf', string $group = 'default'): LoggerInterface
    {
        return di()->get(LoggerFactory::class)->get($name, $group);
    }
}

if (!function_exists('request')) {
    /**
     * 获取请求实例.
     */
    function request(): RequestInterface
    {
        return di()->get(RequestInterface::class);
    }
}

if (!function_exists('esQueryFormat')) {
    /**
     * es搜索闭包.
     * @param mixed $query
     *                     {"query":{"bool":{"must":[],"must_not":[],"should":[{"query_string":{"default_field":"title","query":"春节吃到嗨"}},{"query_string":{"default_field":"label","query":""}}]}},"from":0,"size":250,"sort":[],"aggs":{}}
     */
    function esQueryFormat($query)
    {
        if (!is_array($query)) {
            return $query;
        }
        $params = [
            'must' => [],
            'must_not' => [],
            'should' => [],
            'filter' => [],
        ];

        foreach ($query as $k => $v) {
            if (!is_array($v) || count($v) < 2) {
                continue;
            }
            $key = '';
            $boolArr = [];
            switch ($v[0]) {
                case '||':
                case 'or':
                case '|':
                    $key = 'should';
                    break;
                case 'and':
                case '&&':
                case '&':
                    $key = 'must';
                    break;
                case 'not':
                case '!':
                    $key = 'must_not';
                    break;
                case 'in':
                    $key = 'filter';
                    $boolArr = [['terms' => ["{$k}" => $v[1]]]];
                    break;
                default:
                    continue 2;
            }
            $queryString = $v[1];

            if (empty($queryString)) {
                $queryString = '*';
            }

            if (empty($boolArr)) {
                $boolArr = [['query_string' => ['default_field' => "{$k}", 'query' => "{$queryString}"]]];
            }
            //如果是filter则特殊一点
            $params[$key] = array_merge(
                $params[$key],
                $boolArr
            );
        }
        return json_encode($params);
    }
}

if (!function_exists('esCallback')) {
    /**
     * es搜索闭包.
     * @param mixed $query
     * @param mixed $isFormat
     * @throws TypeError
     */
    function esCallback($query, $isFormat = true): Closure
    {
        return function ($client, $builder, $params) use ($query, $isFormat) {
            //判断query是否是一个json字符串，如果是则json化后并入bool数组内，如果不是则当成字符串操作
            if ($isFormat) {
                $query = es_query_format($query);
            }
            $queryArr = is_json($query, true);
            //如果存在must则优先合并，再覆盖
            if (isset($params['body']['query']['bool']['must']) && !empty($params['body']['query']['bool']['must'])) {
                $queryArr['must'] = array_merge(
                    $queryArr['must'],
                    $params['body']['query']['bool']['must']
                );
            }

            if (isset($params['body']['query']['bool']['should']) && !empty($params['body']['query']['bool']['should'])) {
                $queryArr['should'] = array_merge(
                    $queryArr['should'],
                    $params['body']['query']['bool']['should']
                );
            }

            if (isset($params['body']['query']['bool']['must_not']) && !empty($params['body']['query']['bool']['must_not'])) {
                $queryArr['must_not'] = array_merge(
                    $queryArr['must_not'],
                    $params['body']['query']['bool']['must_not']
                );
            }

            if (isset($params['body']['query']['bool']['filter']) && !empty($params['body']['query']['bool']['filter'])) {
                $queryArr['filter'] = array_merge(
                    $queryArr['filter'],
                    $params['body']['query']['bool']['filter']
                );
            }
            //合并覆盖参数
            if (!empty($queryArr)) {
                $params['body']['query']['bool'] = array_merge(
                    $params['body']['query']['bool'],
                    $queryArr
                );
            } else {
                $params['body']['query']['bool']['should'] = [
                    [
                        'query_string' => [
                            'query' => $query,
                        ],
                    ],
                ];
            }

            echo 'EsSearch:' . json_encode($params);
            return $client->search($params);
        };
    }
}

if (!function_exists('formatEsPageRawData')) {
    /**
     * es搜索闭包.
     * @param mixed $rawData
     * @throws array
     */
    function formatEsPageRawData($rawData): array
    {
        $tmp = [];

        if (isset($rawData['data']['hits']['hits']) && count($rawData['data']['hits']['hits']) > 0) {
            $hitsDataArr = $rawData['data']['hits']['hits'];

            foreach ($hitsDataArr as $value) {
                $tmp[] = $value['_source'] ?? [];
            }
            $rawData['data'] = $tmp;
        }
        return $rawData;
    }
}

if (!function_exists('isJson')) {
    /**
     * 判断字符串是否为 Json 格式.
     *
     * @param string $data Json 字符串
     * @param bool $assoc 是否返回关联数组。默认返回对象
     *
     * @return array|bool|object 成功返回转换后的对象或数组，失败返回 false
     */
    function isJson($data = '', $assoc = false)
    {
        $data = json_decode($data, $assoc);

        if (($data && is_object($data)) || (is_array($data) && !empty($data))) {
            return $data;
        }
        return false;
    }
}

if (!function_exists('snowFlake')) {
    /**
     * 雪花算法生成唯一id.
     */
    function snowFlake(): int
    {
        return di()->get(IdGeneratorInterface::class)->generate();
    }
}

if (!function_exists('getImgPath')) {
    /**
     * 获取图片地址
     * @param string $path 地址
     * @param string $suffix 后缀
     */
    function getImgPath(string $path, string $suffix = ''): string
    {
        if (!str_contains($path, 'http')) {
            $path = phpenv('PUBLIC_DOMAIN') . $path;
        }

        if (empty($suffix)) {
            return $path;
        }
        return $path . '/' . $suffix;
    }
}

if (!function_exists('getImgPathPrivate')) {
    /**
     * 获取七牛加密后的图片地址
     *
     * @param string $path 地址
     */
    function getImgPathPrivate(string $path, int $expires = 3600): string
    {
        $filesystem = di()->get(Filesystem::class);
        //获取私有地址,默认过期一个小时
        return $filesystem->getAdapter()->privateDownloadUrl($path, $expires);
    }
}

if (!function_exists('updateAll')) {
    /**
     * 批量更新数据库(更新数据内必要要有表主键)  主键存在则替换 不存在则插入.
     * @param $data
     * @param $table //包含表前缀的数据表名
     */
    function updateAll($data, $table): bool
    {
        $keyList = array_keys(reset($data));
        $keyStr = implode(',', $keyList);
        $sql = 'replace into ' . $table . "({$keyStr})" . ' values';

        foreach ($data as $item) {
            $sql .= "('" . implode("','", array_values($item)) . "'),";
        }
        $sql = substr($sql, 0, -1);
        return Db::insert($sql);
    }
}

if (!function_exists('exception')) {
    /**
     * 快速抛出异常.
     */
    function exception(string $message, int $code = ErrorCode::ERROR)
    {
        throw new BusinessException($code, $message);
    }
}

if (!function_exists('app')) {
    /**
     * @return mixed|\Psr\Container\ContainerInterface
     * @throws TypeError
     */
    function app(string $abstract = null, array $parameters = [])
    {
        if (\Hyperf\Utils\ApplicationContext::hasContainer()) {
            /** @var \Hyperf\Contract\ContainerInterface $container */
            $container = \Hyperf\Utils\ApplicationContext::getContainer();
            if (is_null($abstract)) {
                return $container;
            }
            if (count($parameters) == 0 && $container->has($abstract)) {
                return $container->get($abstract);
            }
            return $container->make($abstract, $parameters);
        }
        if (is_null($abstract)) {
            throw new \InvalidArgumentException('Invalid argument $abstract');
        }
        return new $abstract(...array_values($parameters));
    }
}

/**
 * redis锁,防止并发操作
 */
if (!function_exists('redisLock')) {
    function redisLock($lock_key = "", $token = "", $expire = 3)
    {
        return ApplicationContext::getContainer()->get(\Redis::class)->set(Constant::REDIS_LOCK_PREFIX . $lock_key, $token, ["nx", 'ex' => $expire]);
    }
}
/**
 * redis解锁
 */
if (!function_exists('redis_unlock')) {
    function redis_unlock($lock_key, $token = "")
    {
        $script = <<<'LUA'
    if redis.call("get",KEYS[1]) == ARGV[1]
        then
            return redis.call("del",KEYS[1])
        else
            return 0
        end
LUA;
        return ApplicationContext::getContainer()->get(\Redis::class)->eval($script, [Constant::REDIS_LOCK_PREFIX . $lock_key, $token], 1);
    }
}

/**
 * 获取客户端ip地址
 * @return mixed
 */
if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        return request()->getHeaderLine('x-real-ip') ?: request()->server('remote_addr');
    }
}

/**
 * 上传图片至aliun oss
 * @return mixed
 */
if (!function_exists('uploadToOss')) {
    function uploadToOss($filename, $filePath)
    {
        try {
            $corsConfig = new \OSS\Model\CorsConfig();
            $rule = new \OSS\Model\CorsRule();
            // 设置允许跨域请求的响应头。AllowedHeader可以设置多个，每个AllowedHeader中最多只能使用一个通配符星号（*）。
            // 建议无特殊需求时设置AllowedHeader为星号（*）。
            $rule->addAllowedHeader("*");
            // 设置允许用户从应用程序中访问的响应头。ExposeHeader可以设置多个，ExposeHeader中不支持使用通配符星号（*）。
            $rule->addExposeHeader("x-oss-header");
            // 设置允许的跨域请求的来源。AllowedOrigin可以设置多个，每个AllowedOrigin中最多只能使用一个通配符星号（*）。
            // $rule->addAllowedOrigin("http://localhost:1024");
            // $rule->addAllowedOrigin("https:/\/*.dzjo.cn");
            // 设置AllowedOrigin为星号（*）时，表示允许所有域的来源。
            $rule->addAllowedOrigin("*");
            // 设置允许的跨域请求方法。
            $rule->addAllowedMethod("POST");
            // 设置浏览器对特定资源的预取（OPTIONS）请求返回结果的缓存时间，单位为秒。
            $rule->setMaxAgeSeconds(10);
            // 每个Bucket最多支持添加10条规则。
            $corsConfig->addRule($rule);
            $ossClient = new \OSS\OssClient(env('ACCESS_KEY_ID'), env('ACCESS_KEY_SECRET'), env('OSS_ENDPOINT'));

            // $url =  $ossClient->putObject(env('OSS_BUCKET'), $filename, file_get_contents($filePath));
            //$url = $ossClient->generatePresignedUrl(env('OSS_BUCKET'), $filename, strtotime("+30 year"));
            $url = $ossClient->putObject(env('OSS_BUCKET'), $filename, file_get_contents($filePath))['info']['url'] ?: null;
        } catch (\OSS\Core\OssException $e) {
            exception($e->getMessage(), 10408);
        }
        return $url ?: "";
    }
}
/**
 * 生成唯一订单号
 * @param $uid
 */
if (!function_exists('buildOrderNo')) {
    function buildOrderNo($uid)
    {
        return phpdate('Ymd') . substr(implode("", array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8) . $uid;
    }
}
/**
 * 获取当前时间戳(毫秒)
 *
 * @return void
 */
if (!function_exists('getMilliSecond')) {
    function getMilliSecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}
if (!function_exists('encodeNo')) {
    // 生成编号
    function encodeNo(int $id, $length = 4)
    {
        $hashids = new \Hashids\Hashids("ryTech", $length, "abCdefg123HiJk456LmnoPqRST789uvWXYZ");
        return $hashids->encode($id);
    }
}

if (!function_exists('decodeNo')) {
    // 解码编号
    function decodeNo($code, $length = 4)
    {
        $hashids = new \Hashids\Hashids("ryTech", $length, "abCdefg123HiJk456LmnoPqRST789uvWXYZ");
        $userId = $hashids->decode($code)[0] ?: 0;
        return $userId;
    }
}
if (!function_exists('formatNumber')) {
    // 保留两位小数
    function formatNumber($number)
    {
        $formatNumber = sprintf("%.2f", $number) ?: 0;
        return $formatNumber;
    }
}

if (!function_exists('getLastAndCurrentWeekDay')) {
    function getLastAndCurrentWeekDay()
    {
        $date = date('Y-m-d'); //当前日期
        $first = 1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $w = date('w', strtotime($date)); //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $nowStart = date('Y-m-d', strtotime("$date -" . ($w ? $w - $first : 6) . ' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $nowEnd = date('Y-m-d', strtotime("$nowStart +6 days")); //本周结束日期
        $lastStart = date('Y-m-d', strtotime("$nowStart - 7 days")); //上周开始日期
        $lastEnd = date('Y-m-d', strtotime("$nowStart - 1 days")); //上周结束日期

        $date = [
            'last_week_start_day' => $lastStart,
            'last_week_end_day' => $lastEnd,
            'current_week_start_day' => $nowStart,
            'current_week_end_day' => $nowEnd,
        ];
        return $date;
    }
}
