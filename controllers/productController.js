const Category = require('../models/Category');
const Product = require('../models/Product');
const { body, validationResult } = require('express-validator');

exports.showAddForm = async (req, res) => {
    const categories = await Category.find().sort('name');
    res.render('add-product', {
        categories,
        errors: res.locals.errors || [],
        oldInput: res.locals.oldInput || {}
    });
};

exports.storeProduct = [
    body('name').notEmpty().withMessage('Name is required'),
    body('description').notEmpty().withMessage('Description is required'),
    body('quantity').isInt({ min: 1 }).withMessage('Quantity must be at least 1'),
    body('categories').custom((value) => {
        if (!value || (Array.isArray(value) && value.length === 0)) {
            throw new Error('At least one category must be selected');
        }
        return true;
    }),

    async (req, res) => {
        const errors = validationResult(req);
        const { name, description, quantity, categories } = req.body;

        if (!errors.isEmpty()) {
            req.flash('errors', errors.array());
            req.flash('oldInput', req.body);
            return res.redirect('/products/create');
        }

        const normalizedName = name.trim().toLowerCase();

        try {
            const product = new Product({
                name: normalizedName,
                description,
                quantity,
                categories: Array.isArray(categories) ? categories : [categories]
            });

            await product.save();
            req.flash('success', 'Product added successfully!');
            res.redirect('/products');
        } catch (err) {
            if (err.code === 11000 && err.keyPattern && err.keyPattern.name) {
                req.flash('errors', [{ msg: 'Product name must be unique' }]);
                req.flash('oldInput', req.body);
                return res.redirect('/products/create');
            }

            res.status(500).send('Server Error');
        }
    }
];


exports.listProducts = async (req, res) => {
    const page = parseInt(req.query.page) || 1;
    const limit = 5;

    const search = req.query.search || '';
    const selectedCategories = req.query.categories || [];

    // Build filter
    const filter = {};

    if (search.trim()) {
        filter.name = { $regex: search.trim(), $options: 'i' }; // case-insensitive search
    }

    if (selectedCategories.length > 0) {
        filter.categories = { $in: Array.isArray(selectedCategories) ? selectedCategories : [selectedCategories] };
    }

    const totalProducts = await Product.countDocuments(filter);
    const totalPages = Math.ceil(totalProducts / limit);

    const products = await Product.find(filter)
        .populate('categories')
        .sort({ createdAt: -1 })
        .skip((page - 1) * limit)
        .limit(limit);

    const allCategories = await Category.find().sort('name');

    res.render('product-list', {
        products,
        currentPage: page,
        totalPages,
        search,
        allCategories,
        selectedCategories: Array.isArray(selectedCategories) ? selectedCategories : [selectedCategories]
    });
};

exports.deleteProduct = async (req, res) => {
    const { id } = req.params;
    await Product.findByIdAndDelete(id);
    req.flash('success', 'Product deleted successfully!');
    res.redirect('/products');
};

