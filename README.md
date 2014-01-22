# InstaClick Base Amqp Bundle

[![Build Status](https://secure.travis-ci.org/instaclick/ICBaseAmqpBundle.png)](http://travis-ci.org/instaclick/ICBaseAmqpBundle)

*IMPORTANT NOTICE:* This bundle is still under development. Any changes will
be done without prior notice to consumers of this package. Of course this
code will become stable at a certain point, but for now, use at your own risk.

## Introduction

This bundle provides Amqp support to Symfony2 applications.

This bundle requires that you are using, at least, Symfony 2.2.

## Installation

Installing this bundle can be done through these simple steps:

1. Add this bundle to your project as a composer dependency:
```javascript
    // composer.json
    {
        // ...
        require: {
            // ...
            "instaclick/base-amqp-bundle": "dev-master"
        }
    }
```

2. Add this bundle in your application kernel:
```php
    // application/ApplicationKernel.php
    public function registerBundles()
    {
        // ...
        $bundles[] = new IC\Bundle\Base\AmqpBundle\ICBaseAmqpBundle();

        return $bundles;
    }
```

3. Double check if the bundle is configured correctly:
```yaml
# application/config/ic_base_amqp.yml
ic_base_amqp:
    connections:
        default:
            host: '%rabbitmq_host%'
            login: '%rabbitmq_user%'
            password: '%rabbitmq_password%'
            port: '%rabbitmq_port%'
            vhost: '%rabbitmq_vhost%'
            persistent: false
    channels:
        default:
            connection: 'default'
            prefetch_size: 0
            prefetch_count: 1
    exchanges:
        manolo_exchange:
            name: 'manolo_exchange'
            auto_declare: true
            channel: 'default'
            type: 'topic'
    queues:
        manolo_queue:
            name: 'manolo_queue'
            auto_declare: true
            channel: 'default'
            binding:
                exchange: 'manolo_exchange'
                routing_key: 'fancy.routing.key'
```

4. Create a service and inject it into the desired service

```
ic_base_amqp.[{queues,exchanges,channels,connections}].[resource_name]

check the following sample:
```

```php
$connection = $this->container->get('ic_base_amqp.connections.default');
$channels   = $this->container->get('ic_base_amqp.channels.default');
$exchanges  = $this->container->get('ic_base_amqp.exchanges.manolo_exchange');
$queue      = $this->container->get('ic_base_amqp.queues.manolo_queue');
```

## Usage

Check the [PHP-Amqp extension documentation Page](http://ca2.php.net/amqp) for a better undestanding of the AMQP API.

