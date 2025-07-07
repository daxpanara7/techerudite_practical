require('dotenv').config();
const mongoose = require('mongoose');
const Category = require('../models/Category');

const categories = [
  { name: 'Electronics' },
  { name: 'Clothing' },
  { name: 'Books' },
  { name: 'Groceries' },
  { name: 'Toys' },
];

mongoose.connect(process.env.MONGO_URI)
  .then(async () => {
    await Category.deleteMany(); 
    await Category.insertMany(categories); 
    console.log('Categories Seeded Successfully');
    mongoose.disconnect();
  })
  .catch(err => {
    console.error('Seeder Error:', err);
  });
