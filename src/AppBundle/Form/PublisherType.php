<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Publisher;

class PublisherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('apiToken')
            ->add('name')
            ->add('skypeId')
            ->add('isSkypeBotActive', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'multiple' => false,
                'expanded' => false,
                'choices' => array(
                    '0' => false,
                    '1' => true,
                ),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Publisher::class,
        ));
    }
}
