<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\DataTransferObject\ProductDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('code')
            ->add('isDiscontinued', CheckboxType::class, [
                'required' => false,
                'label' => 'Is Discontinued',
                ])
            ->add('stock', IntegerType::class)
            ->add('cost', IntegerType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductDTO::class,
        ]);
    }
}
