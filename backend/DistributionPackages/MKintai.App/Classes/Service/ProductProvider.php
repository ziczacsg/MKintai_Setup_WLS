<?php
declare(strict_types=1);

namespace MKintai\App\Service;

/**
 * Nguồn dữ liệu mẫu. Thực tế sẽ là Repository/Doctrine.
 * Cố ý KHÔNG có DI/AOP để Flow không tạo proxy → breakpoint Xdebug dừng chắc chắn (Task 17-7).
 */
class ProductProvider
{
    /** @return array<int, array{id:int, name:string, price:int}> */
    public function all(): array
    {
        $products = [
            ['id' => 1, 'name' => 'Bàn phím cơ', 'price' => 1200000],
            ['id' => 2, 'name' => 'Chuột không dây', 'price' => 450000],
            ['id' => 3, 'name' => 'Màn hình 27"', 'price' => 5600000],
        ];
        return $products;   // ← đặt breakpoint ở dòng này (Task 17-7)
    }
}
