<?php
define('FRAME_BASE_PATH', __DIR__); // 框架目录
define('FRAME_START_TIME', microtime(true)); // 开始时间
define('FRAME_START_MEMORY', memory_get_usage()); // 开始内存

use Psr\Container\ContainerInterface;

class App implements ContainerInterface
{
    /**
     * 绑定关系
     * @var array
     */
    public array $binding = [];

    /**
     * 这个类的实例
     * @var App
     */
    private static ?App $instance = null;

    /**
     * 所有实例的存放
     * @var array
     */
    protected array $instances = [];

    /**
     * App constructor.
     */
    private function __construct()
    {
        self::$instance = $this; // App类的实例
        $this->register();  // 注册绑定
        $this->boot(); // 服务注册了 才能启动
    }

    /**
     * 获取服务
     * @param string $abstract
     * @return mixed
     */
    public function get($abstract)
    {
        // 此服务已经实例化过了
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $instance = $this->binding[$abstract]['concrete']($this); // 因为服务是闭包 加()就可以执行了
        // 设置为单例
        if ($this->binding[$abstract]['is_singleton']) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * 是否有此服务
     * @param string $id 实例id
     * @return bool|void
     */
    public function has($id)
    {
        ;
    }

    /**
     * 当前的App实例  单例
     * @return App
     */
    public static function getContainer()
    {
        return self::$instance ?? self::$instance = new self();
    }

    /**
     * @param string $abstract 就是key
     * @param void|string $concrete 就是value
     * @param boolean $is_singleton 这个服务要不要变成单例
     */
    public function bind($abstract, $concrete, $is_singleton = false)
    {
        // 如果具体实现不是闭包  那就生成闭包
        if (!$concrete instanceof \Closure) {
            $concrete = function ($app) use ($concrete) {
                return $app->build($concrete);
            };
        }
        $this->binding[$abstract] = compact('concrete', 'is_singleton'); // 存到$binding大数组里面
    }

    /**
     * @param $paramters
     * @return array
     */
    protected function getDependencies($paramters)
    {
        // 当前类的所有依赖
        $dependencies = [];
        foreach ($paramters as $paramter) {
            if ($paramter->getClass()) {
                $dependencies[] = $this->get($paramter->getClass()->name);
            }
        }
        return $dependencies;
    }


    /**
     * 解析依赖
     * @param $concrete
     * @return object
     * @throws ReflectionException
     */
    public function build($concrete)
    {
        $reflector = new ReflectionClass($concrete); // 反射
        $constructor = $reflector->getConstructor(); // 获取构造函数
        if (is_null($constructor)) {
            // 没有构造函数？ 那就是没有依赖 直接返回实例
            return $reflector->newInstance();
        }

        $dependencies = $constructor->getParameters(); // 获取构造函数的参数
        $instances = $this->getDependencies($dependencies);  // 当前类的所有实例化的依赖
        return $reflector->newInstanceArgs($instances); // 跟new 类($instances); 一样了
    }

    protected function register()
    {
        $registers = [
            'response' => \core\Response::class,
            'router' => \core\RouteCollection::class,
            'pipeline' => \core\PipeLine::class,
            'config' => \core\Config::class,
            'db' => \core\Database::class,
        ];

        foreach ($registers as $name => $concrete) {
            $this->bind($name, $concrete, true);
        }
    }

    protected function boot()
    {
        App::getContainer()->get('config')->init();
        App::getContainer()->get('router')->group([
            'namespace' => 'App\\controller',
            'middleware' => [
                \App\middleware\WebMiddleWare::class
            ]
        ], function ($router) {
            require_once FRAME_BASE_PATH . '/routes/web.php'; // 因为是require 所以web.php有$router这个变量
        });

        App::getContainer()->get('router')->group([
            'namespace' => 'App\\controller',
            'prefix' => 'api'
        ], function ($router) {
            require_once FRAME_BASE_PATH . '/routes/api.php';
        });
    }
}
