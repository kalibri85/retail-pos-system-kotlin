package com.example.easypos

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.TextView
import android.widget.Toast
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import java.text.DecimalFormat
import java.text.SimpleDateFormat
import java.util.*

class CardPaymentBottomSheet(
    private val totalAmount: Double
) : BottomSheetDialogFragment() {

    private lateinit var tvTotalAmount: TextView
    private lateinit var btnConfirm: Button

    private val decimalFormat = DecimalFormat("0.00")

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        return inflater.inflate(R.layout.bottomsheet_card, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        tvTotalAmount = view.findViewById(R.id.tvTotalAmount)
        btnConfirm = view.findViewById(R.id.btnConfirm)

        tvTotalAmount.text = "Total Amount: GBP ${decimalFormat.format(totalAmount)}"

        btnConfirm.setOnClickListener {
            val main = activity as? MainActivity ?: return@setOnClickListener
            val totals = main.calculateTotals()

            // ---------- Create receipt ----------------------
            main.currentReceipt = Receipt(
                products = main.productList.toList(),
                subtotal = totals.subtotal,
                discount = totals.discountAmount,
                giftCard = main.giftCardAmount,
                total = totals.totalInclVAT,
                paidAmount = totals.totalInclVAT,
                change = 0.0,
                paymentMethod = PaymentMethod.CARD,
                dateTime = SimpleDateFormat(
                    "dd/MM/yyyy HH:mm",
                    Locale.UK
                ).format(Date()),
                receiptNumber = System.currentTimeMillis().toString()
            )

            // Logo
            val logoBitmap = ReceiptFormatter.getPrintableLogo(requireContext())
            //Printer Mac Address
            val printer = BluetoothPrinterHelper("86:67:7A:FD:EA:36")

            try {
                if (printer.connect()) {
                    val textReceipt =
                        ReceiptFormatter.buildTextReceipt(main.currentReceipt)

                    printer.printReceipt(
                        logoBitmap,
                        textReceipt,
                        "https://workstuffuk.com/"
                    )

                    // QR Code
                    printer.feedLines(1)

                    printer.disconnect()

                    Toast.makeText(
                        requireContext(),
                        "Card payment completed",
                        Toast.LENGTH_SHORT
                    ).show()
                } else {
                    Toast.makeText(
                        requireContext(),
                        "Printer not connected",
                        Toast.LENGTH_SHORT
                    ).show()
                }
            } catch (e: Exception) {
                e.printStackTrace()
                Toast.makeText(
                    requireContext(),
                    "Printing failed: ${e.message}",
                    Toast.LENGTH_SHORT
                ).show()
            }

            main.clearCart()
            dismiss()
        }
    }
}
