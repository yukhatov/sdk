<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('storeAppId')
            ->add('virtualCurrency')
            ->add('exchangeRate')
            ->add('isSandBox', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'multiple' => false,
                'expanded' => false,
                'choices' => array(
                    '0' => false,
                    '1' => true,
                ),
            ))
            ->add('isRewarded', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'multiple' => false,
                'expanded' => false,
                'choices' => array(
                    '0' => false,
                    '1' => true,
                ),
            ))
            ->add('isQuickReward', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'multiple' => false,
                'expanded' => false,
                'choices' => array(
                    '0' => false,
                    '1' => true,
                ),
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_application';
    }
}
