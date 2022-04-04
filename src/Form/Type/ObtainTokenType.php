<?php

namespace Sherlockode\SyliusCheckoutPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ObtainTokenType
 */
class ObtainTokenType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('token', HiddenType::class, [
                'label' => false,
                'constraints' => [new NotBlank()],
                'attr' => ['data-contains-checkout-token' => 'true'],
            ]);

        if ($options['allow_persist_instrument']) {
            $builder->add('rememberCard', CheckboxType::class, [
                'label' => 'sherlockode.checkout.remember_card',
                'required' => false,
            ]);
        }

        if (count($options['instruments'])) {
            $choices = [];

            foreach ($options['instruments'] as $instrument) {
                $choices[] = $instrument;
            }

            $builder->add('instrument', ChoiceType::class, [
                'label' => 'sherlockode.checkout.use_an_existing_card',
                'required' => false,
                'choices' => $choices,
                'expanded' => true,
                'choice_label' => function ($choice) {
                    return $choice ? $choice->getLast4() : null;
                },
                'choice_attr' => function ($choice) {
                    return [
                        'data-checkout-existing-card' => 'true',
                        'data-scheme' => $choice ? strtolower($choice->getScheme()) : null,
                    ];
                },
                'choice_value' => function ($choice) {
                    return $choice ? $choice->getId() : null;
                },
                'choice_translation_domain' => false,
                'placeholder' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'instruments' => [],
            'allow_persist_instrument' => true,
        ]);
    }
}
