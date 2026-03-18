package com.example.easypos

import android.bluetooth.BluetoothAdapter
import android.graphics.Bitmap
import com.dantsu.escposprinter.EscPosPrinter
import com.dantsu.escposprinter.connection.bluetooth.BluetoothConnection
import com.dantsu.escposprinter.textparser.PrinterTextParserImg

class BluetoothPrinterHelper(private val macAddress: String) {

    private var connection: BluetoothConnection? = null
    private var printer: EscPosPrinter? = null

    fun connect(): Boolean {
        return try {
            val adapter = BluetoothAdapter.getDefaultAdapter() ?: return false
            val device = adapter.getRemoteDevice(macAddress)

            connection = BluetoothConnection(device)
            connection?.connect()

            printer = EscPosPrinter(
                connection,
                203,   // DPI
                48f,   // Width in mm
                32     // Symbols In Line
            )
            true
        } catch (e: Exception) {
            e.printStackTrace()
            false
        }
    }

    fun printReceipt(logo: Bitmap?, text: String, qrText: String) {
        val printer = printer ?: return

        try {
            val sb = StringBuilder()

            // top margin
            sb.append("\n")

            // logo
            if (logo != null) {
                val logoHex = PrinterTextParserImg.bitmapToHexadecimalString(printer, logo)
                sb.append("\n[C]<img>$logoHex</img>\n")
            }

            // margin after logo
            sb.append("\n")

            // main text
            sb.append(text.trimEnd())
            sb.append("\n\n")

            // QR code
            sb.append("[C]<qrcode size='12'>$qrText</qrcode>")

            sb.append("\n")

            printer.printFormattedText(sb.toString())
            feedLines(1)

        } catch (e: Exception) {
            e.printStackTrace()
        }
    }


    fun printText(text: String) {
        try {
            printer?.printFormattedText(text)
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    fun feedLines(lines: Int) {
        repeat(lines) {
            try {
                printer?.printFormattedText("\n")
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }
    }

    fun disconnect() {
        try {
            connection?.disconnect()
        } catch (_: Exception) {}
    }
}
