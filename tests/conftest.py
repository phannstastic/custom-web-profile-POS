import os
import pytest
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager

@pytest.fixture(scope="session")
def base_url():
    """Mengambil URL target, default ke local PHP server di port 8008"""
    return os.environ.get("BASE_URL", "http://127.0.0.1:8008")

@pytest.fixture(scope="function")
def driver():
    """Inisialisasi WebDriver Chrome untuk setiap test case"""
    chrome_options = Options()
    
    # Headless mode jika dijalankan di CI/CD
    if os.environ.get("HEADLESS", "false").lower() == "true":
        chrome_options.add_argument("--headless=new")
        
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--disable-gpu")
    chrome_options.add_argument("--window-size=1366,768")
    chrome_options.add_argument("--ignore-certificate-errors")

    try:
        service = Service(ChromeDriverManager().install())
        drv = webdriver.Chrome(service=service, options=chrome_options)
    except Exception:
        # Fallback ke default Chrome PATH jika driver manager terkendala koneksi
        drv = webdriver.Chrome(options=chrome_options)
        
    drv.implicitly_wait(5)
    yield drv
    drv.quit()
