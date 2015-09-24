set names utf8;
update f_product set product_img='data/products.jpg';
update f_product set category_img='data/categories2.jpg';
update f_product set category_img='data/pcar.jpg' where category2='车信贷';
update f_product set category_img='data/pcar2.jpg' where category2='车抵押贷';
update f_product set category_img='data/phouse2.jpg' where category2='房信贷';
update f_product set category_img='data/phouse.jpg' where category2='房产抵押贷';
