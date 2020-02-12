<?php

namespace App\Form;

use App\Entity\Announcement;
use App\Entity\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category',EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('title')
            ->add('keywords')
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

            ->add('updated_at')
            ->add('date')
            ->add('status',ChoiceType::class,[
                'choices' => [
                    'True' => 'True',
                    'False' => 'False',
                ]
            ])
            ->add('details', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
            ))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Announcement::class,
            'csrf_protection' => false,
        ]);
    }
}
