# 11 — Bài tổng hợp: schema blog hoàn chỉnh

## Mục tiêu

Tự thiết kế và cài đặt TOÀN BỘ schema domain blog dùng mọi loại quan hệ đã
học (01-10), không có starter code — đây là bài kiểm tra tổng, không phải
bài học mới.

## Yêu cầu domain

Thiết kế và cài đặt (trong package riêng của bạn, ví dụ
`MKintai\App\Domain\Model` — đây là lúc chuyển từ `DoctrineLab` sang package
blog thật, vì đây là bài tổng hợp áp dụng vào dự án chính, khớp Level 2-4-6
trong `MENTOR_PROGRESS.md`):

| Entity | Vai trò | Quan hệ liên quan |
|---|---|---|
| `Post` | Aggregate root chính | 1-N với Comment, N-N với Tag, N-N (qua entity trung gian) với Contributor |
| `Comment` | Con của Post | N-1 tới Post (bài 03: không Repository riêng → auto cascade) |
| `Tag` | Aggregate root độc lập | N-N với Post (bài 05) |
| `User` | Aggregate root, có Profile | 1-1 với Profile (bài 04), tự tham chiếu (follow, bài 07) |
| `Profile` | Con của User | 1-1 với User |
| `Category` | Cây phân cấp | Tự tham chiếu parent/children (bài 07) |
| `Contributor` | Vai trò của User trên Post | N-N với Post qua entity trung gian có `role`, `joinedAt` (bài 06) |

Tự quyết định:
- `Post` có `Category` không (N-1 hay N-N)? `Contributor` có phải chính là
  `User` không, hay là entity riêng?
- Entity nào có Repository riêng (aggregate root) — nhớ hệ quả cascade mặc
  định từ bài 03/08 khi quyết định.
- `Comment` có thể reply lẫn nhau không (self-referencing như bài 07)? Nếu
  có, thiết kế thêm.

## Việc bạn tự làm (không có đáp án sẵn, chỉ có gợi ý kiểm tra)

1. Vẽ ER diagram của schema bạn chọn (tay hoặc Mermaid).
2. Viết toàn bộ entity + Repository.
3. `./flow doctrine:validate` → `./flow doctrine:migrationgenerate` →
   `./flow doctrine:migrate`.
4. Viết 5 truy vấn nghiệp vụ sau bằng DQL/QueryBuilder qua Repository (không
   viết SQL thô, không load hết dữ liệu vào PHP rồi lọc bằng tay):
   - Top 5 Post có nhiều Comment nhất.
   - Tất cả Post của một Tag cụ thể, kèm SỐ LƯỢNG comment mỗi Post, không
     bị N+1 (đo bằng SQL logging như bài 10).
   - Tất cả Contributor của một Post kèm role, sắp xếp theo `joinedAt`.
   - Cây con đầy đủ của một Category (mọi cấp), không N+1 theo độ sâu.
   - Danh sách User mà một User cụ thể đang follow, kèm số Post họ đã viết.
5. Chứng minh (bằng test hoặc script tạm) rằng xóa một `Post` không xóa
   nhầm `Tag` hay `Contributor` (vì chúng là aggregate root độc lập), nhưng
   CÓ xóa `Comment` của nó (vì Comment không có Repository riêng).

## Tiêu chí hoàn thành

- Schema chạy được thật (`doctrine:validate` sạch, migration áp dụng thành
  công).
- Cả 5 truy vấn nghiệp vụ chạy đúng và không N+1 (verify bằng SQL logging).
- Bạn giải thích được, cho MỖI quan hệ trong schema của mình, ai là owning
  side và vì sao.
- Không có entity nào bị cascade/orphanRemoval ngoài ý muốn — bạn CHỌN có ý
  thức, không phải bị Flow áp đặt mà không biết.

Không có file đáp án PHP cho bài này trong `solutions/` — chỉ có checklist
review trong `solutions/11-final-project.md` để bạn tự đối chiếu SAU khi làm
xong, không phải để chép trước.
