package com.example.easypos

class Product (
    val upc: String,
    val name: String,
    val color: String?,
    val size: String?,
    val length: String?,
    var price: Double,
    var priceNoVAT: Double? = null,
    var quantity: Int = 1
)