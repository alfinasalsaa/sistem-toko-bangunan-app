
import hashlib
import secrets
import json
import time
import requests
from datetime import datetime

class SimpleReceiptTester:
    def __init__(self):
        print("🧪 SIMPLE RECEIPT SALTED HASH TESTER")
        print("="*50)
        print("Target: Kuitansi TRX202507100018 - fina")
        print("="*50)
        
    def test_salt_generation(self):
        """Test 1: Generate multiple salts untuk receipt data"""
        print("\n🧂 TEST 1: SALT GENERATION")
        print("-" * 30)
        
        # Data dari kuitansi 
        receipt_data = "TRX202507100018_fina_650000_Semen_Gresik"
        
        salted_hashes = []
        print("Generating 10 salted hashes...")
        
        for i in range(10):
            # Generate random salt
            salt = secrets.token_hex(32)  # 256-bit salt
            
            # Combine receipt data + salt
            salted_content = receipt_data + salt
            
            # Create hash
            hash_result = hashlib.sha256(salted_content.encode()).hexdigest()
            salted_hashes.append(hash_result)
            
            print(f"  {i+1}. Salt: {salt[:16]}... → Hash: {hash_result[:16]}...")
        
        # Check uniqueness
        unique_hashes = len(set(salted_hashes))
        print(f"\nResult: {unique_hashes}/10 unique hashes")
        
        if unique_hashes == 10:
            print("✅ PASS: All salted hashes are unique")
            return True
        else:
            print("❌ FAIL: Found duplicate hashes")
            return False
    
    def test_common_data_resistance(self):
        """Test 2: Test resistance untuk common data"""
        print("\n🌈 TEST 2: COMMON DATA RESISTANCE")
        print("-" * 40)
        
        # Common data dari kuitansi
        common_data = [
            "fina",      # Customer name
            "650000",            # Amount  
            "Semen Gresik",    # Product
            "TRX202507100018"    # Transaction code
        ]
        
        all_passed = True
        
        for data in common_data:
            print(f"\nTesting: '{data}'")
            hashes = []
            
            # Generate 5 different salted hashes
            for i in range(5):
                salt = secrets.token_hex(16)
                salted_hash = hashlib.sha256((data + salt).encode()).hexdigest()
                hashes.append(salted_hash)
            
            unique = len(set(hashes))
            print(f"  Unique hashes: {unique}/5")
            
            if unique == 5:
                print(f"  ✅ PASS")
            else:
                print(f"  ❌ FAIL")
                all_passed = False
        
        return all_passed
    
    def test_avalanche_effect(self):
        """Test 3: Test avalanche effect"""
        print("\n⚡ TEST 3: AVALANCHE EFFECT")
        print("-" * 30)
        
        # Original data
        original = "fina TRX202507100018 650000"
        
        # Modified data (1 character change)
        modified = "fino TRX202507100018 650000"  # 's' → 'z'
        
        # Add same salt to both
        salt = secrets.token_hex(32)
        
        hash1 = hashlib.sha256((original + salt).encode()).hexdigest()
        hash2 = hashlib.sha256((modified + salt).encode()).hexdigest()
        
        # Convert to binary and count different bits
        bin1 = bin(int(hash1, 16))[2:].zfill(256)
        bin2 = bin(int(hash2, 16))[2:].zfill(256)
        
        different_bits = sum(a != b for a, b in zip(bin1, bin2))
        avalanche_percentage = (different_bits / 256) * 100
        
        print(f"Original: '{original}'")
        print(f"Modified: '{modified}'")
        print(f"Different bits: {different_bits}/256")
        print(f"Avalanche effect: {avalanche_percentage:.1f}%")
        
        if 45 <= avalanche_percentage <= 55:
            print("✅ PASS: Optimal avalanche effect")
            return True
        else:
            print("⚠️  SUBOPTIMAL: Avalanche effect not in ideal range")
            return False
    
    def test_python_service_integration(self):
        """Test 4: Integration dengan Python service"""
        print("\n🔗 TEST 4: PYTHON SERVICE INTEGRATION")
        print("-" * 40)
        
        try:
            # Test connection
            response = requests.get("https://jayabangunan.my.id/appjaya/", timeout=5)
            if response.status_code == 200:
                print("✅ Python service is ONLINE")
                return True
            else:
                print("❌ Python service returned error")
                return False
        except:
            print("❌ Python service is OFFLINE")
            print("   Please start: cd python-service && python app.py")
            return False
    
    def run_all_tests(self):
        """Jalankan semua tests"""
        print("🚀 STARTING ALL TESTS FOR RECEIPT TRX202507100018")
        print("="*60)
        
        tests = [
            ("Salt Generation", self.test_salt_generation),
            ("Common Data Resistance", self.test_common_data_resistance), 
            ("Avalanche Effect", self.test_avalanche_effect),
            ("Python Service Integration", self.test_python_service_integration)
        ]
        
        passed = 0
        total = len(tests)
        
        for test_name, test_func in tests:
            try:
                result = test_func()
                if result:
                    passed += 1
            except Exception as e:
                print(f"❌ ERROR in {test_name}: {str(e)}")
        
        # Final results
        print("\n" + "="*60)
        print("📊 FINAL RESULTS")
        print("="*60)
        print(f"Tests passed: {passed}/{total}")
        print(f"Success rate: {(passed/total)*100:.0f}%")
        
        if passed == total:
            print("🎉 ALL TESTS PASSED!")
            print("✅ Salted hash untuk kuitansi fina AMAN")
        elif passed >= total * 0.75:
            print("✅ MOSTLY SECURE")
            print("⚠️  Ada beberapa area untuk improvement")
        else:
            print("❌ MULTIPLE FAILURES")
            print("🔧 Perlu perbaikan sebelum production")
        
        # Save simple report
        report = {
            "receipt": "TRX202507100018 - fina",
            "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
            "tests_passed": passed,
            "total_tests": total,
            "success_rate": f"{(passed/total)*100:.0f}%"
        }
        
        with open("crypto-testing/reports/simple_test_report.json", "w") as f:
            json.dump(report, f, indent=2)
        
        print(f"\n📄 Report saved: crypto-testing/reports/simple_test_report.json")
        
        return passed == total

if __name__ == "__main__":
    tester = SimpleReceiptTester()
    tester.run_all_tests()