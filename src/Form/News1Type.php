<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\News;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class News1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category',EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            // category den hangisini alacaksak onu yazdik burada title
            ->add('title')
            ->add('keywords')
            ->add('description')
            ->add('status',ChoiceType::class,[
                'choices' => [
                    'True' => 'True',
                    'False' => 'False',
                ]
            ])

            ->add('image',FileType::class, [
                'label' => 'Gallery Image',
                'mapped'=> false,
                'required' => false,
                //'class' => News::class,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*', //all image types
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image File',
                    ])
                ],
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
            ->add('detail', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
            ))

            ->add('created_at')
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
