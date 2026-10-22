from selenium.webdriver.common.by import By
from .base_page import BasePage

class PublicMenuPage(BasePage):
    # Locators
    MENU_CARDS = (By.CSS_SELECTOR, ".grid .group.rounded-3xl")
    MODAL_CONTAINER = (By.CSS_SELECTOR, "div[x-show='isModalOpen']")
    MODAL_TITLE = (By.CSS_SELECTOR, "div[x-show='isModalOpen'] h3")
    MODAL_PRICE = (By.CSS_SELECTOR, "div[x-show='isModalOpen'] span.font-mono-num")
    SLIDE_IMAGES = (By.CSS_SELECTOR, "div[x-show='isModalOpen'] .relative img")
    NEXT_SLIDE_BTN = (By.CSS_SELECTOR, "div[x-show='isModalOpen'] button[x-on\\:click*='nextSlide'], div[x-show='isModalOpen'] button[@click*='nextSlide']")
    PREV_SLIDE_BTN = (By.CSS_SELECTOR, "div[x-show='isModalOpen'] button[x-on\\:click*='prevSlide'], div[x-show='isModalOpen'] button[@click*='prevSlide']")
    MODAL_CLOSE_BTN = (By.CSS_SELECTOR, "div[x-show='isModalOpen'] button[x-on\\:click*='closeModal'], div[x-show='isModalOpen'] button[@click*='closeModal']")

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
