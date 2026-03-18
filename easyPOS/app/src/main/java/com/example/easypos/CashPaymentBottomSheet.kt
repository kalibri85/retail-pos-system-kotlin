package com.example.easypos

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.GridLayout
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.FragmentActivity
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import java.text.DecimalFormat
import java.util.*

class CashPaymentBottomSheet(private val totalAmount: Double) : BottomSheetDialogFragment() {

    private lateinit var tvTotalAmount: TextView
    private lateinit var tvCashGiven: TextView
    private lateinit var tvChange: TextView
    private lateinit var btnConfirm: Button
    private lateinit var keypad: GridLayout

    private val inputBuilder = StringBuilder()
    private val decimalFormat = DecimalFormat("0.00")

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        return inflater.inflate(R.layout.bottomsheet_cash, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        tvTotalAmount = view.findViewById(R.id.tvTotal)
        tvCashGiven = view.findViewById(R.id.tvCashGiven)
        tvChange = view.findViewById(R.id.tvChange)
        btnConfirm = view.findViewById(R.id.btnConfirm)
        keypad = view.findViewById(R.id.keypad)

        tvTotalAmount.text = "Total Amount: GBP ${decimalFormat.format(totalAmount)}"
        updateCashDisplay()

        for (i in 0 until keypad.childCount) {
            val button = keypad.getChildAt(i) as Button
            button.setOnClickListener { onKeypadClick(button.text.toString()) }
        }

        btnConfirm.setOnClickListener {
            val main = activity as? MainActivity ?: return@setOnClickListener
            val totals = main.calculateTotals()

            val cashGiven = inputBuilder.toString().toDoubleOrNull() ?: 0.0
            val change = (cashGiven - totalAmount).coerceAtLeast(0.0)

            // ------------- Create receipt ----------------------------------
            main.currentReceipt = Receipt(
                products = main.productList.toList(),
                subtotal = totals.subtotal,
                discount = totals.discountAmount,
                giftCard = main.giftCardAmount,
                total = totals.totalInclVAT,
                paidAmount = cashGiven,
                change = change,
                paymentMethod = PaymentMethod.CASH,
                dateTime = java.text.SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.UK).format(Date()),
                receiptNumber = System.currentTimeMillis().toString()
            )

            // Get Logo With White background
            val logoBitmap = ReceiptFormatter.getPrintableLogo(requireContext())
            //Printer Mac Address
            val printer = BluetoothPrinterHelper("86:67:7A:FD:EA:36")

            try {
                if (printer.connect()) {
                    // Receipt text
                    val textReceipt = ReceiptFormatter.buildTextReceipt(main.currentReceipt)

                    // QR code
                    printer.printReceipt(logoBitmap, textReceipt, "https://workstuffuk.com/")
                    printer.disconnect()
                    Toast.makeText(requireContext(), "Receipt printed successfully", Toast.LENGTH_SHORT).show()
                } else {
                    Toast.makeText(requireContext(), "Printer not connected", Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                e.printStackTrace()
                Toast.makeText(requireContext(), "Printing failed: ${e.message}", Toast.LENGTH_SHORT).show()
            }

            main.clearCart()
            dismiss()
        }
    }
    //------------------- Keypad for Cash Payment ------------------------------------
    private fun onKeypadClick(key: String) {
        when (key) {
            "⌫" -> if (inputBuilder.isNotEmpty()) inputBuilder.deleteCharAt(inputBuilder.length - 1)
            "." -> if (!inputBuilder.contains(".")) {
                if (inputBuilder.isEmpty()) inputBuilder.append("0")
                inputBuilder.append(".")
            }
            else -> inputBuilder.append(key)
        }
        updateCashDisplay()
    }

    private fun updateCashDisplay() {
        val cashGiven = inputBuilder.toString().toDoubleOrNull() ?: 0.0
        tvCashGiven.text = "Cash given: GBP ${decimalFormat.format(cashGiven)}"
        val change = (cashGiven - totalAmount).coerceAtLeast(0.0)
        tvChange.text = "Change: GBP ${decimalFormat.format(change)}"
    }
}
