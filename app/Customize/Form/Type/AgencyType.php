<?php

namespace Customize\Form\Type;

use Customize\Entity\Agency;
use Customize\Entity\Master\AgencyStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgencyType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'required' => false,
            'label' => 'customize.admin.agency.name',
            'class' => 'Customize\Entity\Agency',
            'choice_label' => function (Agency $Agency) {
                $Status = $Agency->getStatus();
                if (null === $Status){
                    return $Agency->getName();
                }
                return $Agency->getStatus()->getId() == AgencyStatus::NON_ACTIVE
                    ? $Agency->getName() . '（非稼働）'
                    : $Agency->getName();
            },
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('a');
            },
            'multiple' => false,
            'expanded' => false,
            'required' => false,
            'placeholder' => 'common.select__unspecified',

        ]);

    }

    public function getParent()
    {
        return EntityType::class;
    }

    public function getBlockPrefix()
    {
        return 'agency';
    }
}
