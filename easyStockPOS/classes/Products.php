<?php
class Products {
    public static function getAll($offset = 0, $perPage = 50, $brandFilter = null, $search = null){
        $pdo = Database::connect();
        $offset = (int)$offset;
        $perPage = (int)$perPage;
        $where = [];
        $params = [];

        if($brandFilter){
            $where[] = "p.brandID = ?";
            $params[] = $brandFilter;
        }
        if($search){
            $where[] = "(s.title LIKE ? OR s.style_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $sqlString = "SELECT p.id, s.style_number, s.title, b.name AS brand, c.colorName as color, siz.name AS size, l.name as fit, p.upc, p.price, p.qty_inventory, p.is_taxable
            FROM products p
            LEFT JOIN style s ON p.styleID = s.id
            LEFT JOIN brand b ON p.brandID = b.id
            LEFT JOIN color c ON p.colorID = c.id
            LEFT JOIN sizes siz ON p.sizeID = siz.id
            LEFT JOIN length l ON p.lengthID = l.id";


        if(!empty($where)){
            $sqlString .= " WHERE ".implode(" AND ", $where);
        }
        $sqlString .= " ORDER BY s.style_number ASC LIMIT $perPage OFFSET $offset";
        $sql = $pdo->prepare($sqlString);
        $sql->execute($params);
        return $sql->fetchAll();
    }
    public static function update($id, $quantity){
        $pdo = Database::connect();
        $sql = $pdo->prepare("UPDATE products SET qty_inventory = ? WHERE id = ?");
        return $sql->execute([$quantity, $id]);
    }
    public static function deleteProducts($id){
        $pdo = Database::connect();
        $sql = $pdo->prepare("DELETE FROM products WHERE id = ?");
        return $sql->execute([$id]);
    }
    public static function countAll($brandFilter = null, $search = null){
        $pdo = Database::connect();
        $where = [];
        $params = [];

        if($brandFilter){
            $where[] = "p.brandID = ?";
            $params[] = $brandFilter;
        }

        if($search){
            $where[] = "(s.title LIKE ? OR s.style_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sqlString = "SELECT COUNT(*) FROM products p LEFT JOIN style s ON p.styleID = s.id";
        if(!empty($where)){
            $sqlString .= " WHERE ".implode(" AND ", $where);
        }
        $sql = $pdo->prepare($sqlString);
        $sql->execute($params);

        return (int)$sql->fetchColumn();
    }
    public static function getBrands(){
        $pdo = Database::connect();
        $sql = $pdo->query("SELECT id, name FROM brand ORDER BY name ASC");
        return $sql->fetchAll();
    }
}
?>