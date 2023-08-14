<?php

    namespace App\Service;

    use App\Entity\Category;

    class CategoryService
    {
        public function arrayCategories(array $categories)
        {
            $data = [];

            foreach ($categories as $category) {
                $data[] = ['id' => $category->getId(), 'libelle' => $category->getLibelle()];
            }

            return $data;
        }

        public function oneCategory(Category $category)
        {
            return $data[] = ['id' => $category->getId(), 'libelle' => $category->getLibelle()];
        }
    }