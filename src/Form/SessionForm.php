<?php

namespace Wexample\SymfonyAi\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyAi\Entity\Session;
use Wexample\SymfonyForms\Form\AbstractForm;
use Wexample\SymfonyForms\Form\Type\TextInputType;

class SessionForm extends AbstractForm
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        // The one thing a human owns on a session: everything else says what
        // the conversation did — who ran it, how far it got, when — and is
        // rewritten by whoever runs the next turn.
        $builder
            ->add(
                'name',
                TextInputType::class,
                [
                    self::FIELD_OPTION_NAME_LABEL => true,
                    self::FIELD_OPTION_NAME_REQUIRED => false,
                ]
            );

        $this->builderAddSubmit($builder);
    }
}
