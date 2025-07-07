const express = require('express');
const router = express.Router();
const productController = require('../controllers/productController');

//Home page
router.get('/', (req, res) => {
  res.render('home');
});

// Product listing
router.get('/products', productController.listProducts);

//Add product
router.get('/products/create', productController.showAddForm);
router.post('/products/create', productController.storeProduct);

// Delete product
router.post('/products/:id/delete', productController.deleteProduct);

module.exports = router;
