<?php


namespace Dochne\Shopping\Repository;


use Dochne\Shopping\Database\Database;
use Dochne\Shopping\Entity\Category;

/**
 * Class CategoryRepository
 * @package Dochne\Shopping\Repository
 * @method hydrate(array $row) : Category
 * @method hydrateMany(array $row) : Category[]
 */
class CategoryRepository extends AbstractRepository
{
    /**
     * @var Database
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return Category[]
     */
    public function all() : array
    {
        return $this->hydrateMany(
            $this->database->query("SELECT * FROM categories ORDER BY position")
        );
    }

    public function find(int $id) : Category
    {
        $categories = $this->database->query("SELECT * FROM categories WHERE id=?", [$id]);
        if (count($categories) === 0) {
            throw new \OutOfBoundsException("Unable to find this category");
        }

        return $this->hydrate($categories[0]);
    }

    public function save(Category $category)
    {
        $this->database->update("categories", [
            "name" => $category->name,
            "position" => $category->position,
            "createdAt" => $category->createdAt,
            "shopping" => $category->shopping
        ], ["id" => $category->id]);
    }
}