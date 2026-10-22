from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

class BasePage:
    def __init__(self, driver):
        self.driver = driver
        self.wait = WebDriverWait(self.driver, 10)

    def navigate_to(self, url):
        self.driver.get(url)

    def get_title(self):
        return self.driver.title

    def find(self, locator):
        return self.wait.until(EC.presence_of_element_located(locator))

    def find_all(self, locator):
        return self.wait.until(EC.presence_of_all_elements_located(locator))

    def click(self, locator):
        el = self.wait.until(EC.element_to_be_clickable(locator))
        el.click()
        return el

    def type(self, locator, text):
        el = self.find(locator)
        el.clear()
        el.send_keys(text)
        return el

    def is_visible(self, locator):
        try:
            return self.wait.until(EC.visibility_of_element_located(locator)).is_displayed()
        except Exception:
            return False
