package com.example.easypos

import android.content.Context
import android.graphics.Bitmap
import android.graphics.Canvas
import android.graphics.Color
import androidx.core.content.ContextCompat
import com.google.zxing.BarcodeFormat
import com.google.zxing.qrcode.QRCodeWriter

object ReceiptFormatter {

    private const val LINE_WIDTH = 32  // line width for 58mm printers

    // ---------------- Text Receipt ----------------
    fun buildTextReceipt(receipt: Receipt): String {
        val sb = StringBuilder()

        sb.append("Date: ${receipt.dateTime}\n")
        sb.append("Receipt: ${receipt.receiptNumber}\n")
        sb.append(line())

        receipt.products.forEach { product ->
            sb.append(formatProduct(product))
        }

        sb.append(line())
        sb.append(row("Subtotal", receipt.subtotal))
        if (receipt.discount > 0) sb.append(row("Discount", -receipt.discount))
        if (receipt.giftCard > 0) sb.append(row("Gift Card", -receipt.giftCard))
        sb.append(row("TOTAL", receipt.total))
        sb.append(line())

        when (receipt.paymentMethod) {
            PaymentMethod.CASH -> {
                sb.append(row("Paid (Cash)", receipt.paidAmount ?: 0.0))
                sb.append(row("Change", receipt.change ?: 0.0))
            }
            PaymentMethod.CARD -> sb.append("Paid by CARD\n")
        }

        sb.append("\nThank you for your purchase!")
        return sb.toString()
    }

    // Logo
    fun getPrintableLogo(context: Context, maxWidth: Int = 384): Bitmap {
        val drawable = ContextCompat.getDrawable(context, R.drawable.logo)!!
        val ratio = drawable.intrinsicWidth.toFloat() / drawable.intrinsicHeight
        val width = maxWidth
        val height = (width / ratio).toInt()

        val bitmap = Bitmap.createBitmap(width, height, Bitmap.Config.ARGB_8888)
        val canvas = Canvas(bitmap)
        canvas.drawColor(Color.WHITE) // white background
        drawable.setBounds(0, 0, width, height)
        drawable.draw(canvas)

        return bitmap
    }

    // QR Code
    fun generateQrBitmap(text: String, size: Int = 256, paddingLines: Int = 2): Bitmap {
        val bitMatrix = QRCodeWriter().encode(text, BarcodeFormat.QR_CODE, size, size)
        val bitmap = Bitmap.createBitmap(size, size, Bitmap.Config.ARGB_8888)
        for (x in 0 until size) {
            for (y in 0 until size) {
                bitmap.setPixel(x, y, if (bitMatrix[x, y]) Color.BLACK else Color.WHITE)
            }
        }
        return bitmap
    }

    // ---------------- Helpers ----------------
    private fun formatProduct(p: Product): String {
        val titleLine = truncate(p.name, LINE_WIDTH) + "\n"
        val details = "${p.color} / ${p.size} / ${p.length}"
        return titleLine + row(details, p.price, p.quantity)
    }

    private fun formatPrice(price: Double): String {
        return "GBP %.2f".format(price)
    }

    private fun row(left: String, amount: Double, quantity: Int = 1): String {
        val priceText = formatPrice(amount)
        val qtyText = if (quantity > 1) " x$quantity" else ""
        val space = LINE_WIDTH - left.length - qtyText.length - priceText.length
        return left + qtyText + " ".repeat(space.coerceAtLeast(1)) + priceText + "\n"
    }

    private fun truncate(text: String, max: Int): String =
        if (text.length > max) text.take(max - 3) + "..." else text

    private fun line() = "-".repeat(LINE_WIDTH) + "\n"
}
