<?php

namespace Sherlockode\SyliusCheckoutPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CheckoutConfigurationType
 */
class CheckoutConfigurationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('public_key', TextType::class, [
                'label' => 'sherlockode.checkout.public_key',
                'constraints' => [new NotBlank()],
            ])
            ->add('secret_key', TextType::class, [
                'label' => 'sherlockode.checkout.secret_key',
                'constraints' => [new NotBlank()],
            ])
            ->add('production', CheckboxType::class, [
                'label' => 'sherlockode.checkout.enable_for_production',
                'required' => false,
            ]);
    }
}
