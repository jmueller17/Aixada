/*******************************************
 * UPGRADE FILE 
 * to switch from Aixada v 2.7. to Aixada 2.8
 * 
 * NOTE: source the dump of v2.7 Then source this file. 
 */

DELIMITER $$

DROP PROCEDURE IF EXISTS dbUpdateUtil_removeRelatedFk $$
CREATE PROCEDURE dbUpdateUtil_removeRelatedFk(in the_table_name varchar(255)) BEGIN
    declare fk_name varchar(255) default ""; 
    declare finished integer default 0;
    declare fk_names cursor for 
        select constraint_name
        from information_schema.key_column_usage
        where
            CONSTRAINT_SCHEMA = DATABASE()
            AND referenced_table_name = the_table_name;
    -- declare not found handler 
    declare continue handler for not found set finished = 1;
    open fk_names;
    read_loop: loop
        fetch fk_names into fk_name;
        IF finished = 1 THEN 
            LEAVE read_loop;
        END IF;
        set @q = concat(
            "alter table aixada_product drop foreign key ", fk_name, ';'
        );
        prepare st from @q;
        execute st;
        deallocate prepare st;
        insert into aixada_version (module_name, version) values (
            concat('> temporarily remove: ', @q), 'removeRelatedFk'
        );
    end loop read_loop;
END $$

DROP PROCEDURE IF EXISTS dbUpdate_280_c01 $$
CREATE PROCEDURE dbUpdate_280_c01() BEGIN

IF NOT EXISTS (
    SELECT * FROM information_schema.tables where table_schema=DATABASE() and table_name='aixada_version'
) THEN

    /**
     * db version + upgrade history
     */
    create table aixada_version (
      id int not null auto_increment,
      module_name varchar(100) default 'main' not null,
      version varchar(42) not null,
      primary key(id)
    ) engine=InnoDB default character set utf8 collate utf8_general_ci;

    insert into aixada_version (version) values ('2.8'); 

END IF;

insert into aixada_version (module_name, version) values (
CONCAT('START dbUpdate_280_c01: ', SYSDATE()), '2.8'); 

IF NOT EXISTS (
    SELECT * FROM information_schema.tables where table_schema=DATABASE() and table_name='aixada_stock_movement_type'
) THEN

    /**
     * Types of stock movements such as stock corrected, loss, etc. 
     */
    create table aixada_stock_movement_type(
      id              int     not null auto_increment,
      name            varchar(30) not null, 
      description     varchar(255),
      primary key (id)
    ) engine=InnoDB default character set utf8 collate utf8_general_ci;


    /**
     * insert default values
     */
    insert into 
        aixada_stock_movement_type (name, description)
    values
        ('SET_ME', 'Temp solution for old movements before stock_movement_type existed.'),
        ('Merma', 'Lo que se pierde por bichos, caidas, caducado, ... '),
        ('Descuadre', 'Lo que no debería pasar pero siempre pasa. '),
        ('Added', 'Llega un pedido de stock y se añade.');
     

    /**
     * make changes to stock_movement in order to reference the movement type. 
     */
    alter table
        aixada_stock_movement
        add movement_type_id int default 1 after operator_id,
        add foreign key (movement_type_id) references aixada_stock_movement_type(id);
    
    insert into aixada_version (module_name, version) values (
    '> CREATE table aixada_stock_movement_type', '2.8');
END IF;

IF EXISTS (
    SELECT c.id FROM aixada_cart c WHERE c.ts_validated <> 0
) AND NOT EXISTS (
    SELECT c.id FROM aixada_order_to_shop ots 
        join (aixada_shop_item si, aixada_cart c) 
        on ots.order_item_id = si.order_item_id and si.cart_id = c.id 
        where c.ts_validated <> 0
) THEN

    /**
     * aixada_order_to_shop now is not a temporal table
     */
    insert into 
        aixada_order_to_shop (
            order_item_id, uf_id, order_id, unit_price_stamp, product_id,
            quantity, arrived, revised
        )
    select
        si.order_item_id, oi.uf_id,oi.order_id, si.unit_price_stamp, si.product_id,
        si.quantity, 1 arrived, 1  revised
    from
        aixada_shop_item si,
        aixada_order_item oi
    where 
        oi.id = si.order_item_id;

    insert into aixada_version (module_name,version) values (
    '> INSERT old orders into aixada_order_to_shop', '2.8');
END IF;

IF NOT EXISTS (
    SELECT * FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='aixada_order_item' AND column_name='iva_percent'
) THEN
    /**
     * "unit_price_stamp": Add 4 decimals, and always supplemented
     *      with `iva_percent` & `rev_tax_percent`.
     */
    ALTER TABLE aixada_order_item 
        MODIFY unit_price_stamp		decimal(14,6)	default 0,
        ADD COLUMN iva_percent 		decimal(5,2)	default 0 AFTER unit_price_stamp,
        ADD COLUMN rev_tax_percent	decimal(5,2)	default 0 AFTER iva_percent;
    ALTER TABLE aixada_shop_item 
        MODIFY unit_price_stamp		decimal(14,6)	default 0;
    ALTER TABLE aixada_order_to_shop
        MODIFY unit_price_stamp		decimal(14,6)	default 0,
        ADD COLUMN iva_percent 		decimal(5,2)	default 0 AFTER unit_price_stamp,
        ADD COLUMN rev_tax_percent	decimal(5,2)	default 0 AFTER iva_percent;
       
    /**
     * Fill `iva_percent` & `rev_tax_percent` in existing records
     *      on `aixada_order_to_shop`.
     */
    SET SQL_SAFE_UPDATES = 0;
    -- from: aixada_shop_item
        update aixada_order_to_shop ots
        join (aixada_shop_item si)
        on ots.order_item_id = si.order_item_id
        set ots.iva_percent = si.iva_percent,
            ots.rev_tax_percent = si.rev_tax_percent
        where ots.iva_percent = 0 and 
            ots.rev_tax_percent = 0;
    -- from: aixada_product (ots.order_item_id not exist on aixada_shop_item)
        update aixada_order_to_shop ots
        left join (aixada_shop_item si)
        on ots.order_item_id = si.order_item_id
        join ( aixada_product p,
            aixada_rev_tax_type rev,
            aixada_iva_type iva)
        on  p.id = ots.product_id and
            rev.id = p.rev_tax_type_id and
            iva.id = p.iva_percent_id
        set ots.iva_percent = iva.percent,
            ots.rev_tax_percent = rev.rev_tax_percent
        where ots.iva_percent = 0 and 
            ots.rev_tax_percent = 0 and 
            si.order_item_id is null;
    SET SQL_SAFE_UPDATES = 1;

    insert into aixada_version (module_name,version) values (
    '> ALTER aixada_order_to_shop & aixada_shop_item: price_stamp 14,6 and add iva% & rev_tax%', '2.8');
END IF;

IF NOT EXISTS (
    SELECT * FROM information_schema.tables where table_schema=DATABASE() and table_name='aixada_account_desc'
) THEN
    /**
     * Account descriptions 
     **/
    create table aixada_account_desc (
      id            smallint    not null auto_increment,
      description   varchar(50) not null,
      account_type  tinyint     default 1, -- 1:treasury, 2:service
      active        tinyint     default 1,
      primary key (id)
    ) engine=InnoDB default character set utf8 collate utf8_general_ci;

    -- create accounts descriptions --
    insert into
        aixada_account_desc (id, description, account_type)
    values
        (1, 'Manteniment',                  2),
        (2, 'Consum (stock adjustments)',   2),
        (3, 'Cashbox',                      1);

    insert into aixada_version (module_name, version) values (
    '> CREATE table aixada_account_desc', '2.8');
END IF;

IF EXISTS (
    SELECT * FROM information_schema.columns
    WHERE table_schema = DATABASE()
        AND table_name ='aixada_unit_measure'
        AND column_name = 'id'
        AND DATA_TYPE = 'tinyint'
) THEN
    /* temporary remove related fk to table aixada_unit_measure */
    CALL dbUpdateUtil_removeRelatedFk('aixada_unit_measure');
    /* alter table */
    alter table aixada_unit_measure
        modify id 	smallint 	not null auto_increment;
    alter table aixada_product
        modify unit_measure_order_id    smallint    default 1,
        modify unit_measure_shop_id     smallint    default 1;
    /* re-create fk */
    alter table aixada_product
        add foreign key (unit_measure_order_id) references aixada_unit_measure(id),
        add foreign key (unit_measure_shop_id)  references aixada_unit_measure(id);
    insert into aixada_version (module_name, version) values (
    '> CHANGE aixada_unit_measure.id from tinyint to smallint ', '2.8');
END IF;

insert into aixada_version (module_name, version) values (
CONCAT('END dbUpdate_280_c01: ', SYSDATE()), '2.8'); 

END $$

CALL dbUpdate_280_c01() $$

DROP PROCEDURE IF EXISTS dbUpdate_280_c01 $$
DELIMITER ;
