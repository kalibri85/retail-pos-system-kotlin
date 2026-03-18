package com.example.easypos

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import android.widget.ImageButton
import androidx.recyclerview.widget.RecyclerView

class ProductAdapter(
    private val context: Context,
    private val items: MutableList<Product>,
    private val onCartChanged: (() -> Unit)? = null
) : RecyclerView.Adapter<ProductAdapter.ProductViewHolder>() {

    class ProductViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val nameText: TextView = view.findViewById(R.id.productName)
        val attrText: TextView = view.findViewById(R.id.productAttributes)
        val priceText: TextView = view.findViewById(R.id.productPrice)
        val qtyText: TextView = view.findViewById(R.id.productQuantity)
        val attributesText: TextView = view.findViewById(R.id.productAttributes)
        val btnDecrease: ImageButton = view.findViewById(R.id.btnDecrease)
        val btnIncrease: ImageButton = view.findViewById(R.id.btnIncrease)
        val btnDelete: ImageButton = view.findViewById(R.id.btnDelete)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ProductViewHolder {
        val view = LayoutInflater.from(context)
            .inflate(R.layout.item_product, parent, false)
        return ProductViewHolder(view)
    }

    override fun onBindViewHolder(holder: ProductViewHolder, position: Int) {
        val item = items[position]

        holder.nameText.text = item.name
        holder.priceText.text = String.format("£%.2f", item.price)
        holder.qtyText.text = "Qty: ${item.quantity}"
        val attributes = listOfNotNull(item.color, item.size, item.length).joinToString(" / ")
        holder.attributesText.text = attributes
        //Decrease
        holder.btnDecrease.setOnClickListener {
            if (item.quantity > 1) {
                item.quantity--
                notifyItemChanged(position)
                onCartChanged?.invoke()
            }
        }

        // Increase
        holder.btnIncrease.setOnClickListener {
            item.quantity++
            notifyItemChanged(position)
            onCartChanged?.invoke()
        }

        // Delete
        holder.btnDelete.setOnClickListener {
            items.removeAt(position)
            notifyItemRemoved(position)
            notifyItemRangeChanged(position, items.size)
            onCartChanged?.invoke()
        }
    }

    override fun getItemCount(): Int = items.size

    fun updateProduct(newProduct: Product) {
        val existing = items.find { it.upc == newProduct.upc }

        if (existing != null) {
            existing.quantity++
        } else {
            items.add(newProduct)
        }
        notifyDataSetChanged()
    }
}
