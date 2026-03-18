package com.example.easypos

data class Totals(
    val subtotal: Double,
    val discountAmount: Double,
    val totalInclVAT: Double,
    val totalExclVAT: Double
)