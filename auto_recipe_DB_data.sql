-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Gostitelj: 127.0.0.1
-- Čas nastanka: 29. jan 2023 ob 12.41
-- Različica strežnika: 10.4.27-MariaDB
-- Različica PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Zbirka podatkov: `auto_recipe`
--

-- --------------------------------------------------------

--
-- Struktura tabele `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredient_ID` int(10) NOT NULL,
  `ingredient_name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_general_ci;

--
-- Odloži podatke za tabelo `ingredients`
--

INSERT INTO `ingredients` (`ingredient_ID`, `ingredient_name`) VALUES
(1, 'water'),
(2, 'milk'),
(3, 'glass'),
(4, 'coca-cola'),
(5, 'enter your first ingredient'),
(6, 'sugar'),
(7, 'flour'),
(8, 'eggs'),
(9, 'salt'),
(10, 'carrots'),
(11, 'tomatoes'),
(12, 'corn'),
(13, 'beans'),
(14, ''),
(15, 'potatoes'),
(16, 'brown sugar'),
(17, 'white sugar'),
(18, 'butter'),
(19, 'vanilla extract'),
(20, 'baking soda'),
(21, 'chocolate chips'),
(22, 'lemon zest'),
(23, 'lemon juice'),
(24, 'baking powder'),
(25, 'spaghetti'),
(26, 'ground beef'),
(27, 'onion'),
(28, 'garlic'),
(29, 'tomato sauce'),
(30, 'tomato paste'),
(31, 'red wine'),
(32, 'pepper'),
(33, 'chicken breast'),
(34, 'bread crumbs'),
(35, 'mozzarella cheese'),
(36, 'parmesan cheese'),
(37, 'oregano'),
(38, 'rice'),
(39, 'vegetable oil'),
(40, 'peas'),
(41, 'soy sauce'),
(42, 'sesame oil'),
(43, 'scallions'),
(44, 'heavy cream'),
(45, 'chicken broth'),
(46, 'basil'),
(47, 'ripe bananas'),
(48, 'blueberries'),
(49, 'powdered sugar'),
(50, 'spinach'),
(51, 'feta cheese'),
(52, 'olive oil'),
(53, 'non-dairy milk'),
(54, 'vegan butter'),
(55, 'vegan chocolate chips'),
(56, 'pasta'),
(57, 'pesto sauce'),
(58, 'cherry tomatoes');

-- --------------------------------------------------------

--
-- Struktura tabele `ingredients_for_recipes`
--

CREATE TABLE `ingredients_for_recipes` (
  `connection_ID` int(10) NOT NULL,
  `recipe_ID` int(10) NOT NULL,
  `ingredient_ID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_general_ci;

--
-- Odloži podatke za tabelo `ingredients_for_recipes`
--

INSERT INTO `ingredients_for_recipes` (`connection_ID`, `recipe_ID`, `ingredient_ID`) VALUES
(1, 1, 3),
(2, 1, 1),
(5, 3, 3),
(6, 3, 4),
(20, 8, 7),
(21, 8, 6),
(23, 8, 8),
(25, 9, 9),
(26, 9, 10),
(27, 9, 11),
(28, 9, 12),
(29, 9, 13),
(32, 8, 2),
(127, 2, 2),
(128, 2, 3),
(131, 15, 7),
(132, 15, 8),
(133, 15, 9),
(134, 15, 16),
(135, 15, 17),
(136, 15, 18),
(137, 15, 19),
(138, 15, 20),
(139, 15, 21),
(140, 16, 7),
(141, 16, 8),
(142, 16, 9),
(143, 16, 16),
(144, 16, 17),
(145, 16, 18),
(146, 16, 19),
(147, 16, 20),
(148, 16, 21),
(149, 17, 2),
(150, 17, 6),
(151, 17, 7),
(152, 17, 8),
(153, 17, 9),
(154, 17, 18),
(155, 17, 22),
(156, 17, 23),
(157, 17, 24),
(158, 18, 9),
(159, 18, 25),
(160, 18, 26),
(161, 18, 27),
(162, 18, 28),
(163, 18, 29),
(164, 18, 30),
(165, 18, 31),
(166, 18, 32),
(167, 19, 7),
(168, 19, 8),
(169, 19, 9),
(170, 19, 29),
(171, 19, 33),
(172, 19, 34),
(173, 19, 35),
(174, 19, 36),
(175, 19, 37),
(176, 20, 9),
(177, 20, 10),
(178, 20, 27),
(179, 20, 38),
(180, 20, 39),
(181, 20, 40),
(182, 20, 41),
(183, 20, 42),
(184, 20, 43),
(185, 21, 6),
(186, 21, 9),
(187, 21, 11),
(188, 21, 18),
(189, 21, 27),
(190, 21, 28),
(191, 21, 44),
(192, 21, 45),
(193, 21, 46),
(194, 22, 6),
(195, 22, 7),
(196, 22, 8),
(197, 22, 9),
(198, 22, 18),
(199, 22, 19),
(200, 22, 20),
(201, 22, 24),
(202, 22, 47),
(203, 23, 2),
(204, 23, 6),
(205, 23, 7),
(206, 23, 8),
(207, 23, 9),
(208, 23, 18),
(209, 23, 19),
(210, 23, 24),
(211, 23, 48),
(212, 24, 6),
(213, 24, 7),
(214, 24, 8),
(215, 24, 18),
(216, 24, 22),
(217, 24, 23),
(218, 24, 49),
(219, 25, 9),
(220, 25, 23),
(221, 25, 28),
(222, 25, 32),
(223, 25, 33),
(224, 25, 50),
(225, 25, 51),
(226, 25, 52),
(227, 26, 6),
(228, 26, 7),
(229, 26, 9),
(230, 26, 19),
(231, 26, 24),
(232, 26, 53),
(233, 26, 54),
(234, 26, 55),
(235, 27, 9),
(236, 27, 32),
(237, 27, 35),
(238, 27, 46),
(239, 27, 52),
(240, 27, 56),
(241, 27, 57),
(242, 27, 58);

-- --------------------------------------------------------

--
-- Struktura tabele `recipes`
--

CREATE TABLE `recipes` (
  `recipe_ID` int(10) NOT NULL,
  `recipe_name` varchar(40) NOT NULL,
  `recipe_data` mediumtext NOT NULL,
  `recipe_thumbnail` varchar(200) NOT NULL,
  `added_by` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_general_ci;

--
-- Odloži podatke za tabelo `recipes`
--

INSERT INTO `recipes` (`recipe_ID`, `recipe_name`, `recipe_data`, `recipe_thumbnail`, `added_by`) VALUES
(1, 'Glass of water', '{\"description\":\"A refreshing glass of water\",\"step1\":{\"step1_text\":\"Get the empty glass\",\"step1_picture\":\"DB_data\\/Pictures\\/1\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Get the water\",\"step2_picture\":\"DB_data\\/Pictures\\/1\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Fill the glass with water\",\"step3_picture\":\"DB_data\\/Pictures\\/1\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Drink the water from the glass\",\"step4_picture\":\"DB_data\\/Pictures\\/1\\/step4.jpg\"}}', 'DB_data/Pictures/1/thumbnail.jpg', 'test1'),
(2, 'Glass of milk', '{\"description\":\"A refreshing glass of milk.\",\"step1\":{\"step1_text\":\"Get the empty glass\",\"step1_picture\":\"DB_data\\/Pictures\\/2\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Get the milk\",\"step2_picture\":\"DB_data\\/Pictures\\/2\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Fill the glass with milk\",\"step3_picture\":\"DB_data\\/Pictures\\/2\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Drink the milk from the glass\",\"step4_picture\":\"DB_data\\/Pictures\\/2\\/step4.jpg\"}}', 'DB_data/Pictures/2/thumbnail.jpg', 'test1'),
(3, 'Glass of Coca-Cola', '{\r\n    \"description\": \"A refreshing glass of Coca-Cola\",\r\n	\"step1\": {\r\n        \"step1_text\": \"Get the empty glass\",\r\n                \"step1_picture\": \"\"\r\n    },\r\n    \"step2\": {\r\n        \"step2_text\": \"Get the Coca-Cola\",\r\n                \"step2_picture\": \"\"\r\n    },\r\n    \"step3\": {\r\n        \"step3_text\": \"Fill the glass with Coca-Cola\",\r\n                \"step3_picture\": \"\"\r\n    },\r\n    \"step4\": {\r\n        \"step4_text\": \"Drink the Coca-Cola from the glass\",\r\n                \"step4_picture\": \"\"\r\n    }\r\n}', 'DB_data/Pictures/3/thumbnail.jpg', 'test2'),
(8, 'Pancakes', '{\"description\":\"Nice sweet pile of pancakes.\",\"step1\":{\"step1_text\":\"Add the flour to the bowl.\",\"step1_picture\":\"DB_data\\/Pictures\\/8\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Add sugar to the bowl.\",\"step2_picture\":\"DB_data\\/Pictures\\/8\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Add the eggs to the bowl.\",\"step3_picture\":\"DB_data\\/Pictures\\/8\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Add milk to the bowl.\",\"step4_picture\":\"DB_data\\/Pictures\\/8\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Mix it all up.\",\"step5_picture\":\"DB_data\\/Pictures\\/8\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Pour the batter into a pan.\",\"step6_picture\":\"DB_data\\/Pictures\\/8\\/step6.jpg\"}}', 'DB_data/Pictures/8/thumbnail.jpg', 'test3'),
(9, 'Vegetable soup', '{\"description\":\"Nice hot bowl of soup.\",\"step1\":{\"step1_text\":\"Set the water to boil.\",\"step1_picture\":\"DB_data\\/Pictures\\/9\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Add salt to the water.\",\"step2_picture\":\"DB_data\\/Pictures\\/9\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Add vegetables to the water.\",\"step3_picture\":\"DB_data\\/Pictures\\/9\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Cook for 2 hours.\",\"step4_picture\":\"DB_data\\/Pictures\\/9\\/step4.jpg\"}}', 'DB_data/Pictures/9/thumbnail.jpg', 'test3'),
(16, 'Chocolate Chip Cookies', '{\"description\":\"These classic chocolate chip cookies are a staple in any home cook\'s recipe collection. Soft and chewy with just the right amount of sweetness, they\'re sure to become a family favorite.\",\"step1\":{\"step1_text\":\"Cream together the butter, brown sugar, and white sugar until smooth.\",\"step1_picture\":\"DB_data\\/Pictures\\/16\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Beat in the eggs, one at a time and then stir in the vanilla extract.\",\"step2_picture\":\"DB_data\\/Pictures\\/16\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Mix together the flour, baking soda, and salt.\",\"step3_picture\":\"DB_data\\/Pictures\\/16\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Gradually add the flour mixture to the butter mixture, mixing well.\",\"step4_picture\":\"DB_data\\/Pictures\\/16\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Stir in the chocolate chips.\",\"step5_picture\":\"DB_data\\/Pictures\\/16\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Preheat the oven to 350 degrees F.\",\"step6_picture\":\"DB_data\\/Pictures\\/16\\/step6.jpg\"},\"step7\":{\"step7_text\":\"Roll the dough into small balls and place them on a baking sheet.\",\"step7_picture\":\"DB_data\\/Pictures\\/16\\/step7.jpg\"},\"step8\":{\"step8_text\":\"Bake for 8-10 minutes or until golden brown.\",\"step8_picture\":\"DB_data\\/Pictures\\/16\\/step8.jpg\"}}', 'DB_data/Pictures/16/thumbnail.jpg', 'test3'),
(17, 'Lemon Pound Cake', '{\"description\":\"This lemon pound cake is a refreshing and delicious dessert, perfect for any occasion. It\'s made with fresh lemon zest and juice, giving it a tangy and zesty flavor.\",\"step1\":{\"step1_text\":\"Cream together the butter and sugar until light and fluffy.\",\"step1_picture\":\"DB_data\\/Pictures\\/17\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Add in the eggs, one at a time, and mix well.\",\"step2_picture\":\"DB_data\\/Pictures\\/17\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Add in the lemon zest, lemon juice, and mix well.\",\"step3_picture\":\"DB_data\\/Pictures\\/17\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Mix together the flour, baking powder, and salt.\",\"step4_picture\":\"DB_data\\/Pictures\\/17\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Gradually add the flour mixture to the butter mixture, alternating with the milk.\",\"step5_picture\":\"DB_data\\/Pictures\\/17\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Preheat the oven to 350 degrees F.\",\"step6_picture\":\"DB_data\\/Pictures\\/17\\/step6.jpg\"},\"step7\":{\"step7_text\":\"Pour the batter into a greased loaf pan.\",\"step7_picture\":\"DB_data\\/Pictures\\/17\\/step7.jpg\"},\"step8\":{\"step8_text\":\"Bake for 45-50 minutes or until a toothpick inserted into the center comes out clean.\",\"step8_picture\":\"DB_data\\/Pictures\\/17\\/step8.jpg\"}}', 'DB_data/Pictures/17/thumbnail.jpg', 'test3'),
(18, 'Spaghetti Bolognese', '{\"description\":\"This spaghetti bolognese is a classic Italian dish that\'s easy to make and full of flavor. It\'s made with ground beef, tomatoes, and red wine, and it\'s sure to be a hit with the whole family.\",\"step1\":{\"step1_text\":\"Cook the spaghetti according to package instructions until al dente.\",\"step1_picture\":\"DB_data\\/Pictures\\/18\\/step1.jpg\"},\"step2\":{\"step2_text\":\"In a pan, brown the ground beef over medium heat until cooked through.\",\"step2_picture\":\"DB_data\\/Pictures\\/18\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Add in the onion and garlic and cook until softened.\",\"step3_picture\":\"DB_data\\/Pictures\\/18\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Add in the tomato sauce, tomato paste, red wine, salt and pepper and bring to a simmer.\",\"step4_picture\":\"DB_data\\/Pictures\\/18\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Cook the sauce for at least 30 minutes, stirring occasionally.\",\"step5_picture\":\"DB_data\\/Pictures\\/18\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Serve the sauce over the cooked spaghetti.\",\"step6_picture\":\"DB_data\\/Pictures\\/18\\/step6.jpg\"}}', 'DB_data/Pictures/18/thumbnail.jpg', 'test2'),
(19, 'Chicken Parmesan', '{\"description\":\"This classic Italian dish is a family favorite. Chicken Parmesan is a delicious and hearty meal that is easy to make. The chicken is breaded and fried, then topped with mozzarella and Parmesan cheese and served with tomato sauce. \",\"step1\":{\"step1_text\":\"Pound the chicken breasts to an even thickness.\",\"step1_picture\":\"DB_data\\/Pictures\\/19\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Set up a breading station with flour, beaten eggs, and bread crumbs.\",\"step2_picture\":\"DB_data\\/Pictures\\/19\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Dredge the chicken in the flour, then dip it in the eggs and coat it with bread crumbs.\",\"step3_picture\":\"DB_data\\/Pictures\\/19\\/step3.jpg\"},\"step4\":{\"step4_text\":\"In a skillet, heat oil over medium heat and fry the chicken for about 5 minutes per side or until golden brown.\",\"step4_picture\":\"DB_data\\/Pictures\\/19\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Transfer the chicken to a baking dish and top each piece with tomato sauce, mozzarella cheese, Parmesan cheese and oregano.\",\"step5_picture\":\"DB_data\\/Pictures\\/19\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Bake in a preheated oven at 350 degrees F for 20-25 minutes or until the cheese is melted and bubbly.\",\"step6_picture\":\"DB_data\\/Pictures\\/19\\/step6.jpg\"}}', 'DB_data/Pictures/19/thumbnail.jpg', 'test1'),
(20, 'Vegetable Fried Rice', '{\"description\":\"This vegetable fried rice is a simple and delicious dish that\'s perfect for a quick and easy weeknight meal. It\'s made with fluffy rice, fresh vegetables, and savory soy sauce and sesame oil. \",\"step1\":{\"step1_text\":\"Cook the rice according to package instructions until tender.\",\"step1_picture\":\"DB_data\\/Pictures\\/20\\/step1.jpg\"},\"step2\":{\"step2_text\":\"In a pan, heat vegetable oil over medium-high heat.\",\"step2_picture\":\"DB_data\\/Pictures\\/20\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Add the onion, carrots and peas and stir-fry for a few minutes or until softened.\",\"step3_picture\":\"DB_data\\/Pictures\\/20\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Add in the cooked rice and stir-fry for 2-3 minutes.\",\"step4_picture\":\"DB_data\\/Pictures\\/20\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Stir in the soy sauce, sesame oil, and salt.\",\"step5_picture\":\"DB_data\\/Pictures\\/20\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Stir in the scallions and cook for a further 1-2 minutes.\",\"step6_picture\":\"DB_data\\/Pictures\\/20\\/step6.jpg\"}}', 'DB_data/Pictures/20/thumbnail.jpg', 'test1'),
(21, 'Creamy Tomato Soup', '{\"description\":\"This creamy tomato soup is a rich and comforting dish that\'s perfect for a chilly day. It\'s made with fresh tomatoes, onion, garlic, butter, heavy cream, and chicken broth, and it\'s garnished with fresh basil.\",\"step1\":{\"step1_text\":\"In a pot, saut\\u00e9 the onion and garlic in butter until softened.\",\"step1_picture\":\"DB_data\\/Pictures\\/21\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Add the tomatoes, chicken broth, sugar, salt, and bring to a simmer.\",\"step2_picture\":\"DB_data\\/Pictures\\/21\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Cook for about 20 minutes, or until the tomatoes are soft.\",\"step3_picture\":\"DB_data\\/Pictures\\/21\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Remove from heat and use an immersion blender to puree the soup until smooth.\",\"step4_picture\":\"DB_data\\/Pictures\\/21\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Stir in the heavy cream and reheat the soup.\",\"step5_picture\":\"DB_data\\/Pictures\\/21\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Serve in bowls and garnish with fresh basil.\",\"step6_picture\":\"DB_data\\/Pictures\\/21\\/step6.jpg\"}}', 'DB_data/Pictures/21/thumbnail.jpg', 'test2'),
(22, 'Banana Bread', '{\"description\":\"This classic banana bread is moist, sweet, and delicious. It\'s perfect for a quick breakfast, a snack, or even as a dessert. It\'s made with ripe bananas, butter, eggs, and vanilla extract, giving it a rich and creamy flavor.\",\"step1\":{\"step1_text\":\"Preheat the oven to 350 degrees F.\",\"step1_picture\":\"DB_data\\/Pictures\\/22\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Mix together the flour, sugar, baking powder, baking soda, and salt.\",\"step2_picture\":\"DB_data\\/Pictures\\/22\\/step2.jpg\"},\"step3\":{\"step3_text\":\"In a separate bowl, mash the bananas.\",\"step3_picture\":\"DB_data\\/Pictures\\/22\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Add in the eggs, butter, and vanilla extract and mix well.\",\"step4_picture\":\"DB_data\\/Pictures\\/22\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Add the banana mixture to the flour mixture and mix until just combined.\",\"step5_picture\":\"DB_data\\/Pictures\\/22\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Pour the batter into a greased loaf pan.\",\"step6_picture\":\"DB_data\\/Pictures\\/22\\/step6.jpg\"},\"step7\":{\"step7_text\":\"Bake for 60-70 minutes or until a toothpick inserted into the center comes out clean.\",\"step7_picture\":\"DB_data\\/Pictures\\/22\\/step7.jpg\"}}', 'DB_data/Pictures/22/thumbnail.jpg', 'test3'),
(23, 'Blueberry Muffins', '{\"description\":\"These blueberry muffins are bursting with fresh blueberries and a hint of vanilla flavor. They are perfect for a sweet breakfast or a tasty snack. They are made with simple ingredients, including fresh blueberries, butter, eggs, and vanilla extract, giving them a rich and creamy flavor.\",\"step1\":{\"step1_text\":\"Preheat the oven to 375 degrees F.\",\"step1_picture\":\"DB_data\\/Pictures\\/23\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Mix together the flour, sugar, baking powder, and salt.\",\"step2_picture\":\"DB_data\\/Pictures\\/23\\/step2.jpg\"},\"step3\":{\"step3_text\":\"In a separate bowl, mix together the milk, eggs, butter, and vanilla extract.\",\"step3_picture\":\"DB_data\\/Pictures\\/23\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Add the wet ingredients to the dry ingredients and mix until just combined.\",\"step4_picture\":\"DB_data\\/Pictures\\/23\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Fold in the blueberries.\",\"step5_picture\":\"DB_data\\/Pictures\\/23\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Fill a muffin tin with the batter.\",\"step6_picture\":\"DB_data\\/Pictures\\/23\\/step6.jpg\"},\"step7\":{\"step7_text\":\"Bake for 20-25 minutes or until a toothpick inserted into the center comes out clean.\",\"step7_picture\":\"DB_data\\/Pictures\\/23\\/step7.jpg\"}}', 'DB_data/Pictures/23/thumbnail.jpg', 'test1'),
(24, 'Lemon Bars', '{\"description\":\"These lemon bars are tangy, sweet, and perfect for a refreshing treat. They are made with a buttery crust and a lemon custard filling. They are easy to make and are sure to be a hit with your family and friends.\",\"step1\":{\"step1_text\":\"Preheat the oven to 350 degrees F.\",\"step1_picture\":\"DB_data\\/Pictures\\/24\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Mix together the flour, sugar, and butter until crumbly.\",\"step2_picture\":\"DB_data\\/Pictures\\/24\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Press the mixture into the bottom of a 9x13 inch baking dish.\",\"step3_picture\":\"DB_data\\/Pictures\\/24\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Bake for 15-20 minutes or until golden brown.\",\"step4_picture\":\"DB_data\\/Pictures\\/24\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Mix together the lemon juice, eggs, powdered sugar, and lemon zest.\",\"step5_picture\":\"DB_data\\/Pictures\\/24\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Pour the lemon mixture over the crust.\",\"step6_picture\":\"DB_data\\/Pictures\\/24\\/step6.jpg\"},\"step7\":{\"step7_text\":\"Bake for an additional 15-20 minutes or until set.\",\"step7_picture\":\"DB_data\\/Pictures\\/24\\/step7.jpg\"},\"step8\":{\"step8_text\":\"Let cool and dust with powdered sugar before serving.\",\"step8_picture\":\"DB_data\\/Pictures\\/24\\/step8.jpg\"}}', 'DB_data/Pictures/24/thumbnail.jpg', 'test1'),
(25, 'Spinach and Feta Stuffed Chicken Breast', '{\"description\":\"This spinach and feta stuffed chicken breast is a delicious and healthy meal that is perfect for a weeknight dinner. The chicken is stuffed with a spinach and feta cheese mixture and flavored with lemon juice, garlic, and olive oil, giving it a delicious and tangy taste.\",\"step1\":{\"step1_text\":\"Preheat the oven to 375 degrees F.\",\"step1_picture\":\"DB_data\\/Pictures\\/25\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Mix together the spinach, feta cheese, lemon juice, garlic, olive oil, salt, and pepper.\",\"step2_picture\":\"DB_data\\/Pictures\\/25\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Slice a pocket into each chicken breast.\",\"step3_picture\":\"DB_data\\/Pictures\\/25\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Stuff each chicken breast with the spinach and feta mixture.\",\"step4_picture\":\"DB_data\\/Pictures\\/25\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Place the chicken breasts in a baking dish.\",\"step5_picture\":\"DB_data\\/Pictures\\/25\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Bake for 25-30 minutes or until the chicken is cooked through.\",\"step6_picture\":\"DB_data\\/Pictures\\/25\\/step6.jpg\"},\"step7\":{\"step7_text\":\"Serve and enjoy!\",\"step7_picture\":\"DB_data\\/Pictures\\/25\\/step7.jpg\"}}', 'DB_data/Pictures/25/thumbnail.jpg', 'test1'),
(26, 'Vegan Chocolate Chip Cookies', '{\"description\":\"These vegan chocolate chip cookies are a delicious and guilt-free treat that are perfect for any occasion. They are made with simple ingredients and are free of animal products, making them suitable for vegans and anyone looking to reduce their animal product consumption.\",\"step1\":{\"step1_text\":\"Preheat the oven to 350 degrees F.\",\"step1_picture\":\"DB_data\\/Pictures\\/26\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Mix together the flour, sugar, baking powder, and salt.\",\"step2_picture\":\"DB_data\\/Pictures\\/26\\/step2.jpg\"},\"step3\":{\"step3_text\":\"In a separate bowl, mix together the non-dairy milk, vegan butter, and vanilla extract.\",\"step3_picture\":\"DB_data\\/Pictures\\/26\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Add the wet ingredients to the dry ingredients and mix until just combined.\",\"step4_picture\":\"DB_data\\/Pictures\\/26\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Fold in the vegan chocolate chips.\",\"step5_picture\":\"DB_data\\/Pictures\\/26\\/step5.jpg\"},\"step6\":{\"step6_text\":\"Drop spoonfuls of the dough onto a baking sheet.\",\"step6_picture\":\"DB_data\\/Pictures\\/26\\/step6.jpg\"},\"step7\":{\"step7_text\":\"Bake for 8-10 minutes or until golden brown.\",\"step7_picture\":\"DB_data\\/Pictures\\/26\\/step7.jpg\"}}', 'DB_data/Pictures/26/thumbnail.jpg', 'test3'),
(27, 'Pesto Pasta Salad', '{\"description\":\"This pesto pasta salad is a delicious and refreshing dish that is perfect for a summer barbecue or potluck. The pasta is mixed with a homemade pesto sauce and paired with cherry tomatoes, mozzarella cheese, and fresh basil, making it a flavorful and satisfying dish.\",\"step1\":{\"step1_text\":\"Cook the pasta according to package instructions until al dente.\",\"step1_picture\":\"DB_data\\/Pictures\\/27\\/step1.jpg\"},\"step2\":{\"step2_text\":\"Mix together the pesto sauce, cherry tomatoes, mozzarella cheese, basil, olive oil, salt, and pepper.\",\"step2_picture\":\"DB_data\\/Pictures\\/27\\/step2.jpg\"},\"step3\":{\"step3_text\":\"Add the pasta to the pesto mixture and toss to combine.\",\"step3_picture\":\"DB_data\\/Pictures\\/27\\/step3.jpg\"},\"step4\":{\"step4_text\":\"Chill the pasta salad for at least 30 minutes before serving.\",\"step4_picture\":\"DB_data\\/Pictures\\/27\\/step4.jpg\"},\"step5\":{\"step5_text\":\"Serve and enjoy!\",\"step5_picture\":\"DB_data\\/Pictures\\/27\\/step5.jpg\"}}', 'DB_data/Pictures/27/thumbnail.jpg', 'test3');

-- --------------------------------------------------------

--
-- Struktura tabele `user_data`
--

CREATE TABLE `user_data` (
  `nickname` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(80) NOT NULL,
  `token` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_general_ci;

--
-- Odloži podatke za tabelo `user_data`
--

INSERT INTO `user_data` (`nickname`, `password`, `email`, `token`) VALUES
('test1', 'test', 'test.test@test.com', 'bba5b536faeacb9b56a3239f1ee8e3b3'),
('test2', 'test', 'test.test@test.com', '488b35b253ada81c53f1be3bb4f04973'),
('test3', 'test', 'test.test@test.com', '58e17d132c6702ef6995faeb8708586e');

-- --------------------------------------------------------

--
-- Struktura tabele `user_favorites`
--

CREATE TABLE `user_favorites` (
  `favorite_ID` int(10) NOT NULL,
  `nickname` varchar(40) NOT NULL,
  `recipe_name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_general_ci;

--
-- Odloži podatke za tabelo `user_favorites`
--

INSERT INTO `user_favorites` (`favorite_ID`, `nickname`, `recipe_name`) VALUES
(1, 'test1', 'Glass of water'),
(2, 'test1', 'Glass of milk'),
(3, 'test2', 'Glass of water'),
(4, 'test2', 'Glass of milk');

--
-- Indeksi zavrženih tabel
--

--
-- Indeksi tabele `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredient_ID`);

--
-- Indeksi tabele `ingredients_for_recipes`
--
ALTER TABLE `ingredients_for_recipes`
  ADD PRIMARY KEY (`connection_ID`),
  ADD KEY `recipe_ID` (`recipe_ID`),
  ADD KEY `ingredient_ID` (`ingredient_ID`);

--
-- Indeksi tabele `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`recipe_ID`),
  ADD KEY `recipe_name` (`recipe_name`);

--
-- Indeksi tabele `user_data`
--
ALTER TABLE `user_data`
  ADD PRIMARY KEY (`nickname`);

--
-- Indeksi tabele `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`favorite_ID`);

--
-- AUTO_INCREMENT zavrženih tabel
--

--
-- AUTO_INCREMENT tabele `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT tabele `ingredients_for_recipes`
--
ALTER TABLE `ingredients_for_recipes`
  MODIFY `connection_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=243;

--
-- AUTO_INCREMENT tabele `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT tabele `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `favorite_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
