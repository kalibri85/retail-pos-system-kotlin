package com.example.easypos

data class Receipt(
    val receiptNumber: String,
    val dateTime: String,
    val products: List<Product>,
    val subtotal: Double,
    val discount: Double,
    val giftCard: Double,
    val total: Double,
    val paidAmount: Double?,
    val change: Double?,
    val paymentMethod: PaymentMethod
)

enum class PaymentMethod {
    CASH, CARD
}