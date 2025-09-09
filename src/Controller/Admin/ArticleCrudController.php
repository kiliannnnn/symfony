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
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use App\Entity\Category;
use App\Entity\Tag;
use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\TagCrudController;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $available = $this->getAvailableCovers();
        $choices = $available ? array_combine($available, $available) : [];

        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextField::new('slug'),
            DateTimeField::new('createdAt')->hideOnForm(),
            ImageField::new('cover')->setBasePath('/uploads/articles')->hideOnForm(),
            TextareaField::new('content')->hideOnIndex(),
            TextField::new('coverFile')->setFormType(VichImageType::class)->onlyOnForms(),
            ChoiceField::new('cover')
                ->setChoices($choices)
                ->onlyOnForms(),
            AssociationField::new('category')->setCrudController(CategoryCrudController::class),
            AssociationField::new('tags')->setCrudController(TagCrudController::class)->setFormTypeOptions(['by_reference' => false]),
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
