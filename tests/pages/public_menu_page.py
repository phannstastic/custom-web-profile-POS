from selenium.webdriver.common.by import By
from .base_page import BasePage

class PublicMenuPage(BasePage):
    # Locators
    MENU_CARDS = (By.CSS_SELECTOR, ".grid .group.rounded-3xl")
    MODAL_CONTAINER = (By.XPATH, "//div[@x-show='isModalOpen']")
    MODAL_TITLE = (By.XPATH, "//div[@x-show='isModalOpen']//h3")
    MODAL_PRICE = (By.XPATH, "//div[@x-show='isModalOpen']//span[contains(@class, 'font-mono-num')]")
    SLIDE_IMAGES = (By.XPATH, "//div[@x-show='isModalOpen']//div[contains(@class, 'relative')]//img")
    NEXT_SLIDE_BTN = (By.XPATH, "//div[@x-show='isModalOpen']//button[contains(@class, 'rounded-full') and .//i[contains(@class, 'fa-chevron-right')]]")
    PREV_SLIDE_BTN = (By.XPATH, "//div[@x-show='isModalOpen']//button[contains(@class, 'rounded-full') and .//i[contains(@class, 'fa-chevron-left')]]")
    MODAL_CLOSE_BTN = (By.XPATH, "//div[@x-show='isModalOpen']//button[contains(@class, 'rounded-full') and .//i[contains(@class, 'fa-xmark')]]")

    def open(self, base_url):
        self.navigate_to(f"{base_url}/menu")

    def get_menu_cards_count(self):
        cards = self.find_all(self.MENU_CARDS)
        return len(cards)

    def click_first_menu_card(self):
        cards = self.find_all(self.MENU_CARDS)
        if cards:
            cards[0].click()

    def is_modal_displayed(self):
        return self.is_visible(self.MODAL_CONTAINER)

    def get_modal_item_title(self):
        el = self.find(self.MODAL_TITLE)
        return el.text.strip()
