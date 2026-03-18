package com.example.easypos
import android.os.Bundle
import android.widget.ImageButton
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.journeyapps.barcodescanner.ScanOptions
import com.journeyapps.barcodescanner.ScanContract
import com.android.volley.Request
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import androidx.recyclerview.widget.RecyclerView
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import android.widget.Button
import android.widget.TextView
import java.util.Locale
import androidx.core.content.ContextCompat
import android.os.Build
import kotlin.div
import kotlin.times

class MainActivity : AppCompatActivity() {

    val productList = mutableListOf<Product>()
    private lateinit var startText: TextView
    private lateinit var productAdapter: ProductAdapter
    private lateinit var totalInclVATText: TextView
    private lateinit var totalExclVATText: TextView
    private lateinit var payButton: Button
    private lateinit var discountText: TextView
    var discountPercent: Int = 0
    lateinit var giftCardAmountText: TextView
    var giftCardAmount: Double = 0.0
    lateinit var currentReceipt: Receipt
    private val barcodeLauncher = registerForActivityResult(ScanContract()) { result ->
        if(result.contents != null){
            val barcode = result.contents
            Toast.makeText(this, "Barcode: $barcode", Toast.LENGTH_SHORT).show()

            // TODO: Send barcode to server API
            searchProductByBarcode(barcode)
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.S) {
            requestPermissions(arrayOf(
                android.Manifest.permission.BLUETOOTH_CONNECT,
                android.Manifest.permission.BLUETOOTH_SCAN
            ), 1001)
        }


        startText = findViewById(R.id.startText)
        totalInclVATText = findViewById(R.id.totalInclVATText)
        totalExclVATText = findViewById(R.id.totalExclVATText)

        val scanButton = findViewById<ImageButton>(R.id.scanButton)
        scanButton.setOnClickListener {
            val options = ScanOptions()
            options.setPrompt("Scan a barcode")
            options.setBeepEnabled(true)
            options.setOrientationLocked(true)
            options.captureActivity = PortraitCaptureActivity::class.java
            barcodeLauncher.launch(options)
        }
        val recyclerView = findViewById<RecyclerView>(R.id.productRecyclerView)
        productAdapter = ProductAdapter(this, productList){
            updateTotal()
        }
        recyclerView.adapter = productAdapter
        recyclerView.layoutManager = LinearLayoutManager(this)
        payButton = findViewById(R.id.payButton)
        payButton.isEnabled = false
        payButton.backgroundTintList = ContextCompat.getColorStateList(this, R.color.gray)
        payButton.setOnClickListener {
            showPaymentDialog()
        }
        discountText = findViewById(R.id.discountText)
        giftCardAmountText = findViewById(R.id.giftCardAmountText)
    }

    private fun searchProductByBarcode(barcode: String) {
        // Link For Getting Data From Database By Barcode
        val url = "http://192.168.0.24/easyStockPOS/getProduct.php?barcode=$barcode"
        //val url = "http://192.22.22.79/easyStockPOS/getProduct.php?barcode=$barcode"

        val request = JsonObjectRequest(
            Request.Method.GET, url, null,
            { response ->
                // parse JSON
                if(response.has("error")){
                    Toast.makeText(this, response.optString("error"), Toast.LENGTH_SHORT).show()
                    return@JsonObjectRequest
                }
                val product = Product(
                    upc = barcode,
                    name = response.optString("name"),
                    color = response.optString("color"),
                    size = response.optString("size"),
                    length = response.optString("length"),
                    price = response.optDouble("price"),
                    priceNoVAT = response.optDouble("priceNoVAT"),
                    quantity = 1
                )
                if (productList.isEmpty()) {
                    startText.visibility = View.GONE
                }
                productAdapter.updateProduct(product)
                updateTotal()
            },
            { error ->
                Toast.makeText(this, "Error: ${error.message}", Toast.LENGTH_SHORT).show()
            }
        )

        // Add Request To The Queue
        Volley.newRequestQueue(this).add(request)
    }
    private fun updateTotal() {
        val totals = calculateTotals()
        //Discount Line
        if (discountPercent > 0) {
            discountText.visibility = View.VISIBLE
            discountText.text = String.format(
                Locale.UK,
                "Discount (%d%%): -£%.2f",
                discountPercent,
                totals.discountAmount
            )
        } else {
            discountText.visibility = View.GONE
        }
        if (giftCardAmount > 0) {
            giftCardAmountText.visibility = View.VISIBLE
            giftCardAmountText.text = "Gift Card used: £%.2f".format(giftCardAmount)
        } else {
            giftCardAmountText.visibility = View.GONE
        }

        totalInclVATText.text =
            "Total incl. VAT: £%.2f".format(totals.totalInclVAT)

        totalExclVATText.text =
            "Total excl. VAT: £%.2f".format(totals.totalExclVAT)

        payButton.isEnabled = totals.totalInclVAT > 0
        payButton.backgroundTintList = ContextCompat.getColorStateList(this, if (totals.totalInclVAT > 0) R.color.green else R.color.gray)
    }
    fun calculateTotals(): Totals {
        val subtotal = productList.sumOf { it.price * it.quantity }
        //Discount
        val discountAmount = if (discountPercent > 0) subtotal * discountPercent / 100 else 0.0
        //Total After Discount And GiftCard
        val totalInclVAT = (subtotal - discountAmount - giftCardAmount)
            .coerceAtLeast(0.0)
        val totalExclVAT = (productList.sumOf { (it.priceNoVAT ?: it.price / 1.2) * it.quantity } - giftCardAmount)
            .coerceAtLeast(0.0)

        return Totals(
            subtotal = subtotal,
            discountAmount = discountAmount,
            totalInclVAT = totalInclVAT,
            totalExclVAT = totalExclVAT
        )
    }
    private fun showPaymentDialog() {
        val sheet = PaymentBottomSheet()
        sheet.show(supportFragmentManager, "PaymentBottomSheet")
    }
    fun applyDiscount(percent: Int) {
        discountPercent = percent
        updateTotal()
    }
    fun applyGiftCard(amount: Double) {
        giftCardAmount = amount
        updateTotal()
    }
    //Clear The Card After Payment
    fun clearCart() {
        productList.clear()
        productAdapter.notifyDataSetChanged()
        startText.visibility = View.VISIBLE
        discountPercent = 0
        giftCardAmount = 0.0
        updateTotal()
    }
}
