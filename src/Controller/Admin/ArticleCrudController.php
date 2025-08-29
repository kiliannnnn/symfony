<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $available = $this->getAvailableCovers();
        // ensure we pass an array to setChoices
        $choices = $available ? array_combine($available, $available) : [];

        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextField::new('slug')->onlyOnIndex(),
            DateTimeField::new('createdAt')->onlyOnIndex(),
            // show a preview on the index
            ImageField::new('cover')->setBasePath('/uploads/articles')->onlyOnIndex(),
            TextareaField::new('content')->hideOnIndex(),
            // Vich image upload field for forms (property on the entity is coverFile)
            TextField::new('coverFile')->setFormType(VichImageType::class)->onlyOnForms(),
            // Allow selecting an existing uploaded cover when editing/creating
            ChoiceField::new('cover')
                ->setChoices($choices)
                ->onlyOnForms(),
        ];
    }

    private function getAvailableCovers(): array
    {
        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/articles';
        $list = [];
        if (is_dir($uploadsDir)) {
            $files = array_values(array_filter(scandir($uploadsDir), fn($f) => ! in_array($f, ['.', '..'])));
            foreach ($files as $f) {
                $list[] = $f;
            }
        }

        return $list;
    }

}
