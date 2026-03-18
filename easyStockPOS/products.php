<?php
require_once 'init.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'] ?? null;
    $selected = $_POST['selected'] ?? [];
    $qty = $_POST['qty'] ?? [];
    if(empty($selected) && empty($qty)){
        $error = "No selected products";
    } else{
        if($action === "update"){
            foreach($selected as $id){
                $newQty = (int)($qty[$id] ?? 0);
                Products::update($id, $newQty);
            }
        }
        if($action === "delete"){
            foreach($selected as $id){
                Products::deleteProducts($id);
            }
        }
    }
}
include 'header.php';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 14;
$offset = ($page-1)*$perPage;
$brandFilter = $_GET['brand_filter'] ?? null;
$search = $_GET['search'] ?? null;
$products = Products::getAll($offset, $perPage, $brandFilter, $search);
$total = Products::countAll($brandFilter, $search);
$totalPage = ceil($total/$perPage);
$brands = Products::getBrands();
?>   
<div class="container-fluid mt-3">
    <div class="row align-items-center mb-3">
        <div class="col-md-4">
            <h2 class="mt-0">Products</h2>
        </div>    
        <div class="col-md-5">
            <form method="get" class="d-flex gap-2 align-items-center">
                <select name="brand_filter" class="form-select form-select-sm">
                    <option value="">All Brands</option>
                    <?php foreach($brands as $brand):?>
                        <option value="<?= $brand['id'] ?>" <?= ($brandFilter == $brand['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand['name']) ?>
                        </option>
                    <?php endforeach; ?>    
                </select>
                <input type="text" name="search" placeholder="Search" class="form-control-sm" value="<?= htmlspecialchars($search ?? '')?>">  
                <button class="btn btn-danger btn-sm">Filter</button>  
            </form>    
        </div>  
        <div class="col-md-3 text-end">
            <button class="btn btn-danger mb-2" id="show-import">Import</button>
            <div id="import-box" class="card p-3 d-none" style="width: 420px;">
                <form action="import.php" method="post" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                    <input type="file" name="csv" accept=".csv" required class="form-control">
                    <button class="btn btn-danger">Upload</button>
                </form>       
            </div> 
        </div>
    </div>        
</div>
<div class="container-fluid mt-3">
    <form method='POST' id='products-form'>
        <!-- Header -->
        <div class="row fw-bold border-bottom pb-2 mb-2 text-nowrap">
            <div class="col-1">
                <input type="checkbox" id='select-all'>
            </div>
            <div class="col-sm-1">Style</div>
            <div class="col-sm-1">Barcode</div>  
            <div class="col-sm-2">Title</div> 
            <div class="col-sm-1">Brand</div>   
            <div class="col-sm-1">Colour</div>  
            <div class="col-sm-1">Size</div>
            <div class="col-sm-1">Fit</div>     
            <div class="col-sm-1">Price £</div>  
            <div class="col-sm-1">Qty</div>
            <div class="col-sm-1">Tax</div>      
        </div>   
        <?php foreach($products as $product): ?>  
         <div class="row align-items-center border-bottom py-2 text-nowrap">
            <!-- Checkbox for each product-->
            <div class="col-1">
                <input type="checkbox" name="selected[]" class="product-checkbox" value="<?= $product['id']?>" data-id="<?= $product['id']?>">
            </div>
            <div class="col-sm-1"><?= htmlspecialchars($product['style_number']) ?></div>
            <div class="col-sm-1"><?= htmlspecialchars($product['upc'] ?? '') ?></div>
            <div class="col-sm-2" style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($product['title']) ?>">
                <?= htmlspecialchars(mb_strimwidth($product['title'], 0, 100, '…')) ?>
            </div>  
            <div class="col-sm-1"><?= htmlspecialchars($product['brand'] ?? '') ?></div> 
            <div class="col-sm-1"><?= htmlspecialchars($product['color']) ?></div>   
            <div class="col-sm-1"><?= htmlspecialchars($product['size']) ?></div>
            <div class="col-sm-1"><?= htmlspecialchars($product['fit']) ?></div>   
            <div class="col-sm-1"><?= (float)($product['price']) ?></div>  
            <div class="col-sm-1"><input type="number" name="qty[<?= (int)$product['id']?>]" value="<?= (int)$product['qty_inventory'] ?>" class="form-control form-control-sm qty-input" data-id="<?= $product['id'] ?>" min="0"></div>
            <div class="col-sm-1"><?= $product['is_taxable'] ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-solid fa-xmark"></i>' ?></div>      
        </div> 
        <?php endforeach; ?>    
        <div class="mt-3 d-flex justify-content-end gap-3">
            <button type="submit" name="action" value="update" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to update quantity for selected products?')">
                Update
            </button>    
            <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete selected products?')">
                Delete
            </button>  
        </div>    
    </form> 
</div>
<nav class="mt-3">
    <ul class="pagination justify-content-center pagination-danger">

        <?php if($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page-1 ?>"><i class="fa-solid fa-angle-left"></i> Prev</a>
            </li>
        <?php endif; ?>

        <?php
        $start = max(1, $page - 2);
        $end   = min($totalPage, $page + 2);

        for($i = $start; $i <= $end; $i++):
        ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if($page < $totalPage): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page+1 ?>">Next <i class="fa-solid fa-angle-right"></i></a>
            </li>
        <?php endif; ?>

    </ul>
</nav>        
<script>
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('input', function(){
            const id = this.dataset.id;
            const checkbox = document.querySelector(
                '.product-checkbox[data-id="' + id + '"]'
            );
            if(checkbox){
                checkbox.checked = true;
            }
        });
    });
    document.getElementById('show-import').addEventListener('click', () =>{
        document.getElementById('import-box').classList.toggle('d-none');
    });
</script>    
<?php include 'footer.php'; ?>