<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Event;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Event1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category',EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('title')
            ->add('description')
            ->add('city', ChoiceType::class,[
                'choices' => [
                    'Istanbul' => 'Istanbul',
                    'Izmir' => 'Izmir',
                    'Bursa' => 'Bursa',
                    'Kırklareli' => 'Kırklareli',
                    'Karabuk' => 'Karabuk',
                    'Sivas' => 'Sivas',
                    'Moscow' => 'Moscow',
                    'Paris' => 'Paris'],
            ])
            ->add('country', ChoiceType::class, [
                'choices' => [
                    'Turkey' => 'Turkey',
                    'America' => 'America',
                    'Syria' => 'Syria',
                    'Russia' => 'Russia',
                    'Germany' => 'Germany',
                    'France' => 'France',
                    'Palestine' => 'Palestine'],
            ])
            ->add('details', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
            ))


            ->add('date')
            ->add('updated_At')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
