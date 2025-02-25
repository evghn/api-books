-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: MariaDB-11.2
-- Время создания: Фев 24 2025 г., 01:18
-- Версия сервера: 11.2.2-MariaDB
-- Версия PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `api_books`
--
CREATE DATABASE IF NOT EXISTS `api_books` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `api_books`;

-- --------------------------------------------------------

--
-- Структура таблицы `book`
--

DROP TABLE IF EXISTS `book`;
CREATE TABLE `book` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_html` varchar(255) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `progress` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_public` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `book`
--

INSERT INTO `book` (`id`, `title`, `author`, `description`, `file_html`, `user_id`, `progress`, `is_public`) VALUES
(1, 'Название 1', 'Автор книги', 'Описание книги', 'books/WldKGwlwd5-7fkuzecWJD6gBv_2NDRha.html', 6, 0, 0),
(2, 'Новое название книги', 'Новый автор книги33', 'Новое описание книги', 'books/oo6dLASL3sMHoa4JLk0LLGTmXJWB3KfX.html', 6, 0, 0),
(3, 'Название 1', 'Автор книги', 'Описание книги', 'books/7cXUpbK4epk2VJ4aJplzRezWSP7Mey08.html', 6, 0, 0),
(4, 'Название 1', 'Автор книги', 'Описание книги', 'books/eFcrnJoVUhdEHsobatX2iFfMNwC1VACO.html', 6, 0, 0),
(5, 'Название 1', 'Автор книги', 'Описание книги', 'books/qYCc3rDGSW2-fvgAMKkrMgt6skLacDVF.html', 5, 0, 0),
(6, 'Название 1', 'Автор книги', 'Описание книги', 'books/Px_CdUFhlDJMEbJ5B8uvkInQrZ_iIjmu.html', 5, 0, 0),
(7, 'Название 1', 'Автор книги', 'Описание книги', 'books/Phbqh_PIuhiDMnQdO9cwJ7tcIgr2xUyn.html', 5, 0, 0),
(8, 'Название 1', 'Автор книги', 'Описание книги', 'books/WldKGwlwd5-7fkuzecWJD6gBv_2NDRha.html', 6, 0, 0),
(9, 'Название 1', 'Автор книги', 'Описание книги', 'books/oo6dLASL3sMHoa4JLk0LLGTmXJWB3KfX.html', 6, 0, 0),
(10, 'Название 1', 'Автор книги', 'Описание книги', 'books/7cXUpbK4epk2VJ4aJplzRezWSP7Mey08.html', 6, 0, 0),
(11, 'Название 1', 'Автор книги', 'Описание книги', 'books/eFcrnJoVUhdEHsobatX2iFfMNwC1VACO.html', 6, 0, 0),
(12, 'Название 1', 'Автор книги', 'Описание книги', 'books/qYCc3rDGSW2-fvgAMKkrMgt6skLacDVF.html', 5, 0, 0),
(13, 'Название 1', 'Автор книги', 'Описание книги', 'books/Px_CdUFhlDJMEbJ5B8uvkInQrZ_iIjmu.html', 5, 0, 0),
(14, 'Название 1', 'Автор книги', 'Описание книги', 'books/Phbqh_PIuhiDMnQdO9cwJ7tcIgr2xUyn.html', 5, 0, 0),
(18, 'Новое название книги', 'Новый автор книги4', 'Новое описание книги', 'books/4AfakEyr9KT-ha8e_p6N_J4Iwn7ns46r.html', 6, 10, 1),
(20, 'Название 1', 'Автор книги', 'Описание книги', 'books/GqtgwJ139_2Dvmbh1kZP59CmAjhb9KIw.html', 6, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `age` tinyint(3) UNSIGNED NOT NULL,
  `gender` tinyint(3) UNSIGNED DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `role` varchar(10) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `age`, `gender`, `password`, `token`, `role`) VALUES
(4, 'Alexey', 'user@prof.ru', 18, NULL, '$2y$13$WQ.2ilhEAJWcJyY/ijtwjOQFzv/32kfHihIWfzmCoG8y3GnLK.1W.', 'AuDR5rmnUKBRSsS9npliGDprAgVLq1JI', 'user'),
(5, 'Admin', 'admin@prof.ru', 18, NULL, '$2y$13$kiel1.O4e/MAkr.Sx/1lL.7h.pBnDLWpsKmpwMz3eK44Zo5SYMG8.', 'nxCVLwHYlgcZnWDUdUx85M4rHnXvdSP-', 'admin'),
(6, 'reader1', 'reader1@prof.ru', 18, NULL, '$2y$13$6qPKb.KH.StP2ASfWgPpnuU8Z.0DPn.Da7ukARuUMSzVhLWFlva2G', 'EhttpvTrb66LV-_vlX7Hq0rfwyf0fnS4', 'user'),
(7, 'reader2', 'reader2@prof.ru', 18, NULL, '$2y$13$iXCdfAZ52eZUcNHYhYp3S.KoXrAdcZpNSntQL9yJ.B383b3eCCbw6', 'hODUc8xTkEY92CdrOB2uL_Br93TgAhDu', 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `user_book_setting`
--

DROP TABLE IF EXISTS `user_book_setting`;
CREATE TABLE `user_book_setting` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `font_family` varchar(255) DEFAULT NULL,
  `font_size` int(10) UNSIGNED DEFAULT NULL,
  `text_color` varchar(7) DEFAULT NULL,
  `background_color` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user_book_setting`
--

INSERT INTO `user_book_setting` (`id`, `user_id`, `font_family`, `font_size`, `text_color`, `background_color`) VALUES
(6, 6, 'Arial', 16, '#000000', '#1FFFFF');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `user_book_setting`
--
ALTER TABLE `user_book_setting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `book`
--
ALTER TABLE `book`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `user_book_setting`
--
ALTER TABLE `user_book_setting`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `book_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_book_setting`
--
ALTER TABLE `user_book_setting`
  ADD CONSTRAINT `user_book_setting_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
