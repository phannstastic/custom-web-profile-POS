from selenium.webdriver.common.by import By
from .base_page import BasePage

class AdminLoginPage(BasePage):
    # Locators
    USERNAME_INPUT = (By.NAME, "username")
    PASSWORD_INPUT = (By.NAME, "password")
    SUBMIT_BUTTON = (By.CSS_SELECTOR, "button[type='submit']")
    ERROR_ALERT = (By.CSS_SELECTOR, ".bg-red-500\\/20, .text-red-300")

    def open(self, base_url):
        self.navigate_to(f"{base_url}/admin/login")

    def login(self, username, password):
        self.type(self.USERNAME_INPUT, username)
        self.type(self.PASSWORD_INPUT, password)
        self.click(self.SUBMIT_BUTTON)

    def is_error_displayed(self):
        return self.is_visible(self.ERROR_ALERT)
