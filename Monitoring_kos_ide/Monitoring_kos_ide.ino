#include <WiFi.h>
#include <HTTPClient.h>
#include <PZEM004Tv30.h>

#define RXD2_PIN 16
#define TXD2_PIN 17
 
#define RXD1_PIN 26 
#define TXD1_PIN 27

HardwareSerial SerialPZEM1(2);
HardwareSerial SerialPZEM2(1);

PZEM004Tv30 pzem1(SerialPZEM1, RXD2_PIN, TXD2_PIN);
PZEM004Tv30 pzem2(SerialPZEM2, RXD1_PIN, TXD1_PIN);

const char* ssid = "Trisna";
const char* password = "satu2tiga";
String server = "http://10.194.248.93/monitoring_kos/simpan_data.php";

float dayaSebelumnya1 = 0;
float dayaSebelumnya2 = 0;
float threshold = 2.0;

void kirimData(String kamar, float v, float a, float p, float e) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = server + "?kamar=" + kamar +
                 "&tegangan=" + String(v, 2) +
                 "&arus="     + String(a, 2) +
                 "&daya="     + String(p, 2) +
                 "&energi="   + String(e, 4);
    http.begin(url);
    int code = http.GET();
    Serial.printf("Kirim %s -> HTTP %d\n", kamar.c_str(), code);
    http.end();
  }
}

void setup() {
  Serial.begin(115200);
  delay(1000);

  SerialPZEM1.begin(9600, SERIAL_8N1, RXD2_PIN, TXD2_PIN);
  delay(100);

  SerialPZEM2.begin(9600, SERIAL_8N1, RXD1_PIN, TXD1_PIN);
  delay(100);

  Serial.println("PZEM Serial initialized");

  WiFi.begin(ssid, password);
  Serial.print("Menghubungkan WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nWiFi Terhubung");
}

void loop() {
  float v1 = pzem1.voltage();
  float a1 = pzem1.current();
  float p1 = pzem1.power();
  float e1 = pzem1.energy();

  Serial.printf("[PZEM1] V:%.2f A:%.2f W:%.2f kWh:%.4f\n",
    isnan(v1)?0:v1, isnan(a1)?0:a1, isnan(p1)?0:p1, isnan(e1)?0:e1);

  float v2 = pzem2.voltage();
  float a2 = pzem2.current();
  float p2 = pzem2.power();
  float e2 = pzem2.energy();

  Serial.printf("[PZEM2] V:%.2f A:%.2f W:%.2f kWh:%.4f\n",
    isnan(v2)?0:v2, isnan(a2)?0:a2, isnan(p2)?0:p2, isnan(e2)?0:e2);

  if (!isnan(p1) && abs(p1 - dayaSebelumnya1) > threshold) {
    kirimData("kamar_1", v1, a1, p1, e1);
    dayaSebelumnya1 = p1;
  }

  if (!isnan(p2) && abs(p2 - dayaSebelumnya2) > threshold) {
    kirimData("kamar_2", v2, a2, p2, e2);
    dayaSebelumnya2 = p2;
  }

  delay(3000);
}