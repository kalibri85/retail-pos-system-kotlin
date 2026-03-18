package com.example.easypos
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import android.widget.LinearLayout
import android.widget.Toast
class PaymentBottomSheet : BottomSheetDialogFragment() {

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        return inflater.inflate(R.layout.bottomsheet_payment, container, false)
    }
    private fun applyDiscount(percent: Int) {
            val main = activity as? MainActivity
            if (main != null) {
                main.applyDiscount(percent)
                dismiss()
            } else {
                Toast.makeText(context, "Error: MainActivity not found", Toast.LENGTH_SHORT).show()
            }
    }
    fun applyGiftCard(amount: Double) {
        val main = activity as? MainActivity
        if (main != null) {
            main.applyGiftCard(amount)
            dismiss()
        } else {
            Toast.makeText(context, "Error: MainActivity not found", Toast.LENGTH_SHORT).show()
        }

    }
    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val btnCash = view.findViewById<Button>(R.id.btnCash)
        val btnCard = view.findViewById<Button>(R.id.btnCard)
        val btnGift = view.findViewById<Button>(R.id.btnGift)
        val btnDiscount = view.findViewById<Button>(R.id.btnDiscount)

        val discountContainer = view.findViewById<LinearLayout>(R.id.discountContainer)
        val giftCardContainer = view.findViewById<LinearLayout>(R.id.giftCardContainer)

        view.findViewById<Button>(R.id.btn5).setOnClickListener { applyDiscount(5) }
        view.findViewById<Button>(R.id.btn10).setOnClickListener { applyDiscount(10) }
        view.findViewById<Button>(R.id.btn15).setOnClickListener { applyDiscount(15) }
        view.findViewById<Button>(R.id.btn20).setOnClickListener { applyDiscount(20) }
        view.findViewById<Button>(R.id.btn25).setOnClickListener { applyDiscount(25) }
        view.findViewById<Button>(R.id.btn30).setOnClickListener { applyDiscount(30) }
        view.findViewById<Button>(R.id.btn50).setOnClickListener { applyDiscount(50) }
        view.findViewById<Button>(R.id.btn75).setOnClickListener { applyDiscount(75) }

        view.findViewById<Button>(R.id.gc15).setOnClickListener { applyGiftCard(15.0) }
        view.findViewById<Button>(R.id.gc20).setOnClickListener { applyGiftCard(20.0) }
        view.findViewById<Button>(R.id.gc25).setOnClickListener { applyGiftCard(25.0) }
        view.findViewById<Button>(R.id.gc30).setOnClickListener { applyGiftCard(30.0) }
        view.findViewById<Button>(R.id.gc40).setOnClickListener { applyGiftCard(40.0) }
        view.findViewById<Button>(R.id.gc50).setOnClickListener { applyGiftCard(50.0) }

        btnCash.setOnClickListener {
            dismiss()
            val main = activity as? MainActivity

            val total = main?.calculateTotals()?.totalInclVAT ?: 0.0
            CashPaymentBottomSheet(total)
                .show(parentFragmentManager, "CashSheet")
        }

        btnCard.setOnClickListener {
            val main = activity as? MainActivity
            if (main != null) {
                CardPaymentBottomSheet(main.calculateTotals().totalInclVAT)
                    .show(parentFragmentManager, "CardSheet")
                dismiss()
            }
        }


        btnGift.setOnClickListener {
            giftCardContainer.visibility = if (giftCardContainer.visibility == View.GONE) View.VISIBLE else View.GONE
        }
        btnDiscount.setOnClickListener {
            discountContainer.visibility =
                if (discountContainer.visibility == View.GONE) View.VISIBLE else View.GONE
        }



    }
}