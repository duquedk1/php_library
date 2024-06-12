<?php

//classes
//master class resources
abstract class Resource
{
    //variables Declaration
    protected $id;
    protected $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    abstract public function display();
}

//Independent class Author
class Author
{
    //variables Declaration
    private $id;
    private $name;
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function display()
    {
        echo "\n";
        echo "Author ID: $this->id, Name: $this->name\n";
    }
}
//resource child class
class Book extends Resource
{
    private $publisher;
    private $author;
    public function __construct($id, $name, $publisher, Author $author)
    {
        parent::__construct($id, $name);
        $this->publisher = $publisher;
        $this->author = $author;
    }
    public function getPublisher()
    {
        return $this->publisher;
    }
    public function getAuthor()
    {
        return $this->author;
    }
    public function display()
    {
        echo "\n";
        echo "Book ID: $this->id, Name: $this->name, Publisher: $this->publisher\n";
        $this->author->display();
        echo "\n";
        
    }
}
//resource child class
class OtherResource extends Resource
{
    private $type;
    public function __construct($id, $name, $type)
    {
        parent::__construct($id, $name);
        $this->type = $type;
    }
    public function getType()
    {
        return $this->type;
    }
    public function display()
    {
        echo "\n";
        echo "Resource ID: $this->id, Name: $this->name, Type: $this->type \n";
    }
}
function lJson($db)
{
    if (file_exists($db)) {
        return json_decode(file_get_contents($db), true);
    } else {
        echo "\n";
        echo "The database is empty: \n";
        return [];
    }
}
function sJson($db, $data)
{
    file_put_contents($db, json_encode($data, JSON_PRETTY_PRINT));
}
function dispMenu()
{
    echo "\n";
    echo "Library Management System\n";
    echo "1-Book List\n";
    echo "2-Resource List\n";
    echo "3-Add Book\n";
    echo "4-Add Resource\n";
    echo "5-Delete Book\n";
    echo "6-Delete Resource\n";
    echo "7-Search Book with ID\n";
    echo "8-Sort Book in Ascending order\n";
    echo "9-Sort Book in Descending order\n";
    echo "10-Exit the program\n";
    echo "Enter your choice: ";

}
function genBookList()
{
    $books = lJson("dbBooks.json");
    foreach ($books as $bookData) {
        $author = new Author($bookData['author']['id'], $bookData['author']['name']);
        $book = new Book($bookData['id'], $bookData['name'], $bookData['publisher'], $author);
        $book->display();
    }
}
function genResourceList()
{
    $resources = ljson("dbresources.json");
    foreach ($resources as $resourceData) {
        $resource = new OtherResource($resourceData['id'], $resourceData['name'], $resourceData['type']);
        $resource->display();
    }
}
function addNewBook()
{
    $id = readline('Enter Book ID: ');
    $name = readline('Enter Book Name: ');
    $publisher = readline('Enter Publisher Name: ');
    $authorId = readline('Enter Author ID: ');
    $authorName = readline('Enter Author Name: ');


    $author = new Author($authorId, $authorName);
    $book = new Book($id, $name, $publisher, $author);

    $books = lJson("dbBooks.json");
    $books[] = [
        'id' => $book->getId(),
        'name' => $book->getName(),
        'publisher' => $book->getPublisher(),
        'author' => [
            'id' => $author->getId(),
            'name' => $author->getName()
        ]
    ];
    sJson("dbBooks.json", $books);
    echo "\n";
    echo "Book Added successfully ";
    echo "\n";
}
function addNewResource()
{
    $id = readline('Enter Resource ID: ');
    $name = readline('Enter Resource Name: ');
    $type = readline('Enter Resource Type: ');

    $resource = new OtherResource($id, $name, $type);

    $resources = lJson('dbResources.json');
    $resources[] = [
        'id' => $resource->getId(),
        'name' => $resource->getName(),
        'type' => $resource->getType()
    ];
    sJson('dbResources.json', $resources);
    echo "Resource added successfully!\n";
}
function deleteBook()
{
    $id = readline("Enter Book ID : ");
    $books = lJson('dbBooks.json');
    $books = array_filter($books, function ($book) use ($id) {
        return $book['id'] !== $id;
    });
    sJson('dbBooks.json', array_values($books));
    echo "\n";
    echo "Book deleted successfully!\n";
    echo "\n";
}
function deleteResource()
{
    $id = readline('Enter Resource ID: ');
    $resources = lJson('dbResources.json');
    $resources = array_filter($resources, function ($resource) use ($id) {
        return $resource['id'] !== $id;
    });
    sJson('dbResources.json', array_values($resources));
    echo "\n";
    echo "Resource deleted successfully!\n";
    echo "\n";
}
function searchBookById()
{
    $id = readline('Enter Book ID: ');
    $books = lJson('dbBooks.json');
    foreach ($books as $bookData) {
        if ($bookData['id'] == $id) {
            $author = new Author($bookData['author']['id'], $bookData['author']['name']);
            $book = new Book($bookData['id'], $bookData['name'], $bookData['publisher'], $author);
            $book->display();
            return;
        }
    }
    echo "\n";
    echo "Book not found.\n";
}
function sortBooks($order = 'asc', $criteria = 'name')
{
    $books = lJson('dbBooks.json');
    usort($books, function ($a, $b) use ($order, $criteria) {
        $valueA = ($criteria === 'author') ? $a['author']['name'] : $a[$criteria];
        $valueB = ($criteria === 'author') ? $b['author']['name'] : $b[$criteria];
        return $order === 'asc' ? strcmp($valueA, $valueB) : strcmp($valueB, $valueA);
    });
    foreach ($books as $bookData) {
        $author = new Author($bookData['author']['id'], $bookData['author']['name']);
        $book = new Book($bookData['id'], $bookData['name'], $bookData['publisher'], $author);
        $book->display();
    }
}


while (true) {
    dispMenu();
    $choice = trim(fgets(STDIN));

    switch ($choice) {
        case 1:
            genBookList();
            break;
        case 2:
            genResourceList();
            break;
        case 3:
            addNewBook();
            break;
        case 4:
            addNewResource();
            break;
        case 5:
            deleteBook();
            break;
        case 6:
            deleteResource();
            break;
        case 7:
            searchBookById();
            break;
        case 8:
            $criteria = readline("Enter criteria for sorting (title or author): ");
            if ($criteria !== 'title' && $criteria !== 'author') {
                echo "Invalid criteria. Please enter 'title' or 'author'.\n";
                break;
            }
            sortBooks('asc', $criteria === 'title' ? 'name' : 'author');
            break;
        case 9:
            $criteria = readline("Enter criteria for sorting (title or author): ");
            if ($criteria !== 'title' && $criteria !== 'author') {
                echo "Invalid criteria. Please enter 'title' or 'author'.\n";
                break;
            }
            sortBooks('desc', $criteria === 'title' ? 'name' : 'author');
            break;
        case 10:
            exit("Exiting the program.\n");
        default:
            echo "Invalid choice. Please try again.\n";
    }
}