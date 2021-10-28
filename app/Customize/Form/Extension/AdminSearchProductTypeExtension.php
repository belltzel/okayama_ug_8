<?php

namespace Customize\Form\Extension;

use Customize\Form\Type\AgencyType;
use Eccube\Form\Type\Admin\SearchProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class AdminSearchProductTypeExtension extends AbstractTypeExtension
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('agency', AgencyType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return SearchProductType::class;
    }

}
