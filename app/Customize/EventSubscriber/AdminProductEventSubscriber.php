<?php
namespace Customize\EventSubscriber;

use Eccube\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * Class AdminCustomerEventSubscriber
 * @package Customize\EventSubscriber
 */
class AdminProductEventSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            '@admin/Product/index.twig' => 'onAdminProductIndexTwig',
            '@admin/Product/product.twig' => 'onAdminProductProductTwig',
        ];
    }

    public function onAdminProductIndexTwig(TemplateEvent $event)
    {
        $event->addSnippet('@admin/Snippet/Product/index.twig');
    }

    public function onAdminProductProductTwig(TemplateEvent $event)
    {
        $event->addSnippet('@admin/Snippet/Product/product.twig');
    }

}
