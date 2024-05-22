<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Form\Type;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use PrestaShop\Module\Logviewer\Domain\Enum\LogLevel;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShop\Module\Logviewer\Domain\Enum\LogContext;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;

class ConfigurationFormType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Logviewer_Strategy', ChoiceType::class, [
                'label' => $this->trans('Strategy', 'Modules.Logviewer.Admin'),
                'choices' => [
                    $this->trans('History', 'Modules.Logviewer.Admin') => 'history',
                    $this->trans('Tail', 'Modules.Logviewer.Admin') => 'tail',
                ],
                'multiple' => false,
                'expanded' => false,
                'required' => true,
            ])
            ->add('Logviewer_History_Days', TextType::class, [
                'label' => $this->trans('View logs for the past', 'Modules.Logviewer.Admin'),
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[\d]+$/',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new Range([
                        'min' => 1,
                        'max' => $options['max_days'],
                        'notInRangeMessage' => $this->trans(
                            'This value should be between %min% and %max%.',
                            'Admin.Notifications.Error',
                            [
                                '%min%' => 1,
                                '%max%' => $options['max_days'],
                            ]
                        ),
                    ]),
                ],
                'required' => true,
            ])
            ->add('Logviewer_Tail_Lines', TextType::class, [
                'label' => $this->trans('Fetch the last', 'Modules.Logviewer.Admin'),
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[\d]+$/',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new Range([
                        'min' => $options['min_lines'],
                        'max' => $options['max_lines'],
                        'notInRangeMessage' => $this->trans(
                            'This value should be between %min% and %max%.',
                            'Admin.Notifications.Error',
                            [
                                '%min%' => $options['min_lines'],
                                '%max%' => $options['max_lines'],
                            ]
                        ),
                    ]),
                ],
                'required' => true,
            ])
            ->add('Logviewer_Log_Contexts', MaterialChoiceTableType::class, [
                'label' => $this->trans('Log contexts', 'Modules.Logviewer.Admin'),
                'choices' => LogContext::array(),
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new Count([
                        'min' => 1,
                    ]),
                ],
                'required' => true,
            ])
            ->add('Logviewer_Log_Levels', MaterialChoiceTableType::class, [
                'label' => $this->trans('Log levels', 'Modules.Logviewer.Admin'),
                'choices' => LogLevel::array(),
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new Count([
                        'min' => 1,
                    ]),
                ],
                'required' => true,
            ])
            ->add('Logviewer_Exception_History_Days', TextType::class, [
                'label' => $this->trans('View exceptions for the past', 'Modules.Logviewer.Admin'),
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[\d]+$/',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new Range([
                        'min' => 1,
                        'max' => $options['max_days'],
                        'notInRangeMessage' => $this->trans(
                            'This value should be between %min% and %max%.',
                            'Admin.Notifications.Error',
                            [
                                '%min%' => 1,
                                '%max%' => $options['max_days'],
                            ]
                        ),
                    ]),
                ],
                'required' => true,
            ])
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $data = $event->getData();
                    $strategy = $data['Logviewer_Strategy'];
                    if ('history' === $strategy) {
                        $form->add(
                            'Logviewer_Tail_Lines', 
                            TextType::class,
                            [
                                'required' => false,
                            ]
                        );
                    } else if ('tail' === $strategy) {
                        $form->add(
                            'Logviewer_History_Days', 
                            TextType::class,
                            [
                                'required' => false,
                            ]
                        );
                    }
                }
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'max_days' => null,
                'min_lines' => null,
                'max_lines' => null,
            ])
            ->setAllowedTypes('max_days', ['int', 'null'])
            ->setAllowedTypes('min_lines', ['int', 'null'])
            ->setAllowedTypes('max_lines', ['int', 'null'])
        ;
    }

}
