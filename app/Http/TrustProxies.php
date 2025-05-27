    <?php

    namespace App\Http\Middleware;

    use Illuminate\Http\Middleware\TrustProxies as Middleware;
    use Illuminate\Http\Request; // O use Fideloper\Proxy\TrustProxies as Middleware; en versiones más antiguas de Laravel

    class TrustProxies extends Middleware
    {
        /**
         * The trusted proxies for this application.
         *
         * Para plataformas como Railway, Heroku, AWS ELB, etc.,
         * donde no conoces las IPs exactas de los proxies o pueden cambiar,
         * es común confiar en todos los proxies.
         *
         * @var array<int, string>|string|null
         */
        protected $proxies = '*'; // Confía en todos los proxies

        /**
         * The headers that should be used to detect proxies.
         *
         * @var int
         */
        protected $headers =
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB; // Esta última es más para AWS, pero no daña tenerla
    }
    