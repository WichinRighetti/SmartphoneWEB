use smartphones; 
drop procedure if exists spBuyPhone;
delimiter $$ 
create procedure spBuyPhone(
in inDeviceId int,
in inQuantity int, 
out result varchar(50)
)

begin 

		declare price float;
        declare quantity int;
        
 		-- error handling 
        declare exit handler for sqlexception begin
			set result = 'SQL Error'; -- SQL error 
            rollback; -- rollback all changes
            end;
            
            -- declare result 
            set result = 'OK';
			
            -- start transaction 
            Select UnitPrice into price FROM device where Id = inDeviceId;
            Select Stock into quantity FROM device Where id = inDeviceId;
            if inQuantity > quantity then
					set result = 'Insufficent Stock, reduce quantity';
			end if; 
            if result = 'OK' then
            UPDATE device Set Stock = (Stock - inQuantity) Where Id = inDeviceId;
            set price = price * inQuantity; 
            insert into sale (DeviceId, Quantity, Total, Status) values (inDeviceId, inQuantity, price, 'SOLD');
            end if;
            
        -- commit changes 
        if result = 'OK' then
			commit; -- commmit transactions
        else 
			rollback;
		end if;
    end$$
delimiter  ;

-- Call spBuyPhone(3, 40, @result);
-- Select @result
