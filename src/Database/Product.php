<?php

namespace App\Database;

use App\Utils\ImageConstants;
use App\Utils\TypeProduct;
use \Faker\Factory;
use \Mmo\Faker\FakeimgProvider;

class Product extends QueryExecutor
{
    private int $id;
    private string $name;
    private string $description;
    private string $image;
    private string $type;
    private int $stock;

    public static function read(): array
    {
        return parent::executeQuery("SELECT * FROM products", "Unable to retrieve products")->fetchAll();
    }

    public function create(): void
    {
        parent::executeQuery(
            "INSERT INTO products (name, description, image, type, stock) VALUES (:n, :d, :i, :t, :s)",
            "Failed to create product '{$this->name}'",
            [
                ":n" => $this->name,
                ":d" => $this->description,
                ":i" => $this->image,
                ":t" => $this->type,
                ":s" => $this->stock,
            ]
        );
    }

    public function update(int $id): void
    {
        $statements = [
            ":i" => $id,
            ":n" => $this->name,
            ":d" => $this->description,
            ":t" => $this->type,
            ":im" => $this->image,
            ":s" => $this->stock,
        ];
        parent::executeQuery("UPDATE products SET name = :n, description = :d, image = :im, stock = :s, type = :t WHERE id = :i", "Failed to update product with ID '$id'", $statements);
    }

    public static function resetToDefaultImage(int $id): void
    {
        parent::executeQuery("UPDATE products SET image = :im WHERE id = :i", "Failed to reset product image to default", [":im" => "img/" . ImageConstants::DEFAULT_IMAGE_FILENAME, ":i" => $id]);
    }

    public static function delete(int $id): void
    {
        parent::executeQuery("DELETE FROM products WHERE id = :i", "Failed to delete product with ID '$id'", [":i" => $id]);
    }

    public static function generateFakeProducts(int $amount): void
    {
        $faker = Factory::create("es_ES");
        $faker->addProvider(new FakeimgProvider($faker));
        for ($i = 0; $i < $amount; $i++) {
            $name = ucwords($faker->unique()->words(random_int(1, 3), true));
            $initials = implode(array_map(fn($word) => $word[0], explode(" ", $name)));
            (new Product)
                ->setName($name)
                ->setDescription($faker->text())
                ->setImage($faker->fakeImg(dir: __DIR__ . "/../../public/img/", width: 640, height: 640, fullPath: false, text: $initials, backgroundColor: [random_int(0, 255), random_int(0, 255), random_int(0, 255)]))
                ->setType($faker->randomElement(TypeProduct::cases())->toString())
                ->setStock(random_int(0, 100))
                ->create();
        }
    }

    public static function findById(int $id): array
    {
        return parent::executeQuery(
            "SELECT * FROM products WHERE id = :id",
            "Failed to retrieve product with ID '$id'",
            [":id" => $id]
        )->fetch();
    }

    public static function isFieldUnique(string $field, string $value, ?int $id = null): bool
    {
        $query = $id
            ? "SELECT COUNT(*) AS total FROM products WHERE $field = :value AND id != :id"
            : "SELECT COUNT(*) AS total FROM products WHERE $field = :value";

        $params = $id ? [":value" => $value, ":id" => $id] : [":value" => $value];
        return !parent::executeQuery($query, "Failed to check uniqueness of $field '$value'", $params)->fetchColumn();
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of image
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Set the value of image
     */
    public function setImage(?string $image = null): self
    {
        $this->image = ($image) ? "img/$image" : "img/default.png";

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the value of type
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of stock
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * Set the value of stock
     */
    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }
}
