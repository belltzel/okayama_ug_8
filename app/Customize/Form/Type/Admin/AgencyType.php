<?php

namespace Customize\Form\Type\Admin;

use Customize\Form\Type\Master\AgencyStatusType;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AgencyType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * CustomerType constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EccubeConfig $eccubeConfig
    )
    {
        $this->entityManager = $entityManager;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('code', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => $this->eccubeConfig['customize_agency_code_min_len'],
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => "/^[A-Za-z0-9]+$/u",
                        'message' => 'form_error.graph_only',
                    ]),
                ],
            ])
            ->add('margin_rate', NumberType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range(['min' => 0]),
                    new Assert\Regex([
                        'pattern' => "/^\d+(\.\d+)?$/u",
                        'message' => 'form_error.float_only',
                    ]),
                ],
                'scale' => 1,
            ])
            ->add('remarks', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('status', AgencyStatusType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $Agency = $event->getData();

                // 更新の場合はcodeの項目を削除
                if (null !== $Agency->getId()) {
                    $form->remove('code');
                }

            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $Agency = $event->getData();

                // 代理店コードの重複チェック
                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('count(a)')
                    ->from('Customize\\Entity\\Agency', 'a')
                    ->where('a.code = :code')
                    ->setParameter('code', $Agency->getCode());

                // 更新の場合は自身のデータを重複チェックから除外する
                if (null !== $Agency->getId()) {
                    $qb
                        ->andWhere('a.id <> :agency_id')
                        ->setParameter('agency_id', $Agency->getId());
                }

                $count = $qb->getQuery()->getSingleScalarResult();
                if ($count > 0) {
                    $form['code']->addError(new FormError(trans('customize.form_error.agency_code_already_exists')));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Customize\Entity\Agency',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'customize_admin_agency';
    }
}
