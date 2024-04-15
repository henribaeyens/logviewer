<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

 namespace PrestaShop\Module\Logviewer\Admin\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShop\Module\Logviewer\Domain\Entity\LogEntry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;

class ConfigurationFormType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Logviewer_History_Days', TextType::class, [
                'label' => $this->trans('View logs for the past', 'Modules.Logviewer.Admin'),
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[\d]{1}$/',
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
                                '%min%' => 0,
                                '%max%' => $options['max_days'],
                            ]
                        ),
                    ]),
                ],
                'required' => true,
            ])
            ->add('Logviewer_Log_Contexts', MaterialChoiceTableType::class, [
                'label' => $this->trans('Log contexts', 'Modules.Logviewer.Admin'),
                'choices' => LogEntry::CONTEXTS,
                'multiple' => true,
                'expanded' => true,
                'required' => true,
            ])
            ->add('Logviewer_Log_Levels', MaterialChoiceTableType::class, [
                'label' => $this->trans('Log levels', 'Modules.Logviewer.Admin'),
                'choices' => LogEntry::LEVELS,
                'multiple' => true,
                'expanded' => true,
                'required' => true,
            ])
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
            ])
            ->setAllowedTypes('max_days', ['int', 'null'])
        ;
    }

}
